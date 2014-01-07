<?php

class UserModel extends BaseModel {

    private $_intBlockedTime;

    public function __construct() {
        $this->_intBlockedTime = 300;

    }


    private function _checkUserDataExists($strUsername, $strEmail) {
        $intExisting = 0;

        $strCheckExistingStatement = "SELECT COUNT(id) FROM " . $this->_strTablePrefix . "users WHERE email LIKE :email "
            . "OR user_name LIKE :uname";
        $objExistUserPDO = $this->_objPDO->prepare($strCheckExistingStatement);
        $objExistUserPDO->bindValue(':email', $strEmail, PDO::PARAM_STR);
        $objExistUserPDO->bindValue(':uname', $strUsername, PDO::PARAM_STR);
        if($objExistUserPDO->execute()) {
            $intExisting = $objExistUserPDO->fetchColumn();
        }
        return (0 < $intExisting);

    }


    public function createNewUser($strUsername, $strPassword, $strFirstname, $strLastname, $strEmail) {

        $strNewUserStatement = "INSERT INTO " . $this->_strTablePrefix . "users ( `user_name`, `first_name`, "
            . "`last_name`, `email`, `last_attempt`, `password`, `salt`, `ip_address`, `reset_token` ) "
            . "VALUES ( :uname, :fname, :lname, :email, :time, :pass, :salt, :ip, :token )";

        $objNewUserPDO = $this->_objPDO->prepare($strNewUserStatement);

        $strSalt = Data::createSalt(20);
        $strHashedPassword = hash('sha256', $strPassword.$strSalt);
        $strToken = substr($strHashedPassword, 16, 10);
        $strClientIp = $_SERVER['REMOTE_ADDR'];
        $intClientIpLong =  ip2long($strClientIp);
        $intTimestamp = time();

        $objNewUserPDO->bindValue(':uname', $strUsername, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':fname', $strFirstname, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':lname', $strLastname, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':email', $strEmail, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':pass', $strHashedPassword, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':time', $intTimestamp, PDO::PARAM_INT);
        $objNewUserPDO->bindValue(':salt', $strSalt, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':ip', $intClientIpLong, PDO::PARAM_INT);
        $objNewUserPDO->bindValue(':token', $strToken, PDO::PARAM_STR);

        if($objNewUserPDO->execute()) {
            return true;
        }
        else{
            return false;
        }

    }

    public function resetUserPassword($strNewPassword, $strEmail, $strToken) {
        $strResetStatement = "UPDATE " . $this->_strTablePrefix . "users SET `password` = :pass , "
            . "`reset_token` = :newtoken, `last_attempt` = :time, `ip_address` = :ip, `salt` = :salt "
            . "WHERE `email` LIKE :email AND `reset_token` LIKE :oldtoken";

        $objNewPasswordPDO = $this->_objPDO->prepare($strResetStatement);

        $strSalt = Data::createSalt(20);
        $strHashedPassword = hash('sha256', $strNewPassword.$strSalt);
        $strToken = substr($strHashedPassword, 16, 10);
        $strClientIp = $_SERVER['REMOTE_ADDR'];
        $intClientIpLong =  ip2long($strClientIp);
        $intTimestamp = time();

        $objNewPasswordPDO->bindValue(':pass', $strHashedPassword, PDO::PARAM_STR);
        $objNewPasswordPDO->bindValue(':newtoken', $strToken, PDO::PARAM_STR);
        $objNewPasswordPDO->bindValue(':email', $strEmail, PDO::PARAM_STR);
        $objNewPasswordPDO->bindValue(':ip', $intClientIpLong, PDO::PARAM_INT);
        $objNewPasswordPDO->bindValue(':salt', $strSalt, PDO::PARAM_STR);
        $objNewPasswordPDO->bindValue(':time', $intTimestamp, PDO::PARAM_INT);
        $objNewPasswordPDO->bindValue(':oldtoken', $strToken, PDO::PARAM_STR);
        if($objNewPasswordPDO->execute()) {
            return true;
        }
        else{
            return false;
        }
    }

    public function userLoggedOut() {
        if(session_destroy()) {
            return true;
        }
        else {
            return false;
        }
    }

    public function userLoggedIn($strUsername, $strSalt, $strPassword) {
        $strLoginStatement = "SELECT id, user_name, is_admin FROM " . $this->_strTablePrefix . "users "
            . "WHERE user_name LIKE :uname AND password LIKE :pass";

        $objLoginPDO = $this->_objPDO->prepare($strLoginStatement);

        $strHashedPassword = $this->_getHashedPassword($strSalt, $strPassword);

        $objLoginPDO->bindValue(':uname', $strUsername, PDO::PARAM_STR);
        $objLoginPDO->bindValue(':pass', $strHashedPassword, PDO::PARAM_STR);
        if($objLoginPDO->execute()) {
            $arrUserData = $objLoginPDO->fetch(PDO::FETCH_ASSOC);
            if(!$arrUserData) {
                return false;
            }
            $intUserId = (int)$arrUserData['id'];
            if(is_int($intUserId) && $intUserId > 0) {
                $arrSessionData['blnLoggedIn'] = true;
                $arrSessionData['intUserId'] = $intUserId;
                $arrSessionData['blnIsAdmin'] = (bool)$arrUserData['is_admin'];
                $arrSessionData['strUsername'] = $arrUserData['user_name'];
                return $arrSessionData;
            }
        }

        return false;
    }

    public function getClientIpLong() {
        $strClientIp = $_SERVER['REMOTE_ADDR'];
        return ip2long($strClientIp);
    }

    private function _getHashedPassword($strSalt, $strPassword) {
        return hash('sha256', $strPassword.$strSalt);
    }

    public function setFailedAttempt($strUsername, $intClientIpLong, $intAttempts) {
        $intTimestamp = time();
        $intAttempts += 1;
        $strResetStatement = "UPDATE " . $this->_strTablePrefix . "users SET `ip_address` = :ip , "
            . "`last_attempt` = :time, `failed_attempts` = :attempts WHERE `user_name` LIKE :uname";

        $objResetAttemptsPDO = $this->_objPDO->prepare($strResetStatement);
        $objResetAttemptsPDO->bindValue(':uname', $strUsername, PDO::PARAM_STR);
        $objResetAttemptsPDO->bindValue(':time', $intTimestamp, PDO::PARAM_INT);
        $objResetAttemptsPDO->bindValue(':attempts', $intAttempts, PDO::PARAM_INT);
        $objResetAttemptsPDO->bindValue(':ip', $intClientIpLong, PDO::PARAM_INT);
        if($objResetAttemptsPDO->execute()) {
            return true;
        }
        else {
            return false;
        }

    }

    public function getUserLoginData($strUsername) {
        $strSaltStatement = "SELECT salt, failed_attempts, last_attempt, ip_address FROM " . $this->_strTablePrefix . "users "
            . "WHERE user_name LIKE :uname";
        $objLoginDataPDO = $this->_objPDO->prepare($strSaltStatement);
        $objLoginDataPDO->bindValue(':uname', $strUsername, PDO::PARAM_STR);
        if($objLoginDataPDO->execute()) {
            $arrLoginData = $objLoginDataPDO->fetch(PDO::FETCH_ASSOC);
            if($arrLoginData) {
                return $arrLoginData;
            }
        }
        return false;
    }

    public function checkUserIsBlocked($intAttempts, $intLastIpLong, $intLastAttemptTime) {

        $intUserIpLong = $this->getClientIpLong();
        if($intAttempts > 2) {
            if($intLastIpLong !== $intUserIpLong) {
                return false;
            }
            if(($intLastAttemptTime + $this->_intBlockedTime) > time()) {
                return true;
            }
        }
        return false;

    }

    public function resetLoginAttempts($intUserId) {
        $intTimestamp = time();
        $intClientIpLong = $this->getClientIpLong();
        $strResetStatement = "UPDATE " . $this->_strTablePrefix . "users SET `ip_address` = :ip , "
            . "`last_attempt` = :time, `failed_attempts` = 0 WHERE `id` = :userid";

        $objResetAttemptsPDO = $this->_objPDO->prepare($strResetStatement);
        $objResetAttemptsPDO->bindValue(':userid', $intUserId, PDO::PARAM_INT);
        $objResetAttemptsPDO->bindValue(':time', $intTimestamp, PDO::PARAM_INT);
        $objResetAttemptsPDO->bindValue(':ip', $intClientIpLong, PDO::PARAM_INT);
        if($objResetAttemptsPDO->execute()) {
            return true;
        }
        else {
            return false;
        }
    }


}

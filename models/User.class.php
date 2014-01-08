<?php

class UserModel extends BaseModel {

    private $_intBlockedTime;
    protected $_strTablePrefix;
    protected $_objPDO;

    public function __construct(BaseModel &$objBaseModel) {
        $this->_objPDO = $objBaseModel->_objPDO;
        $this->_intBlockedTime = 300;
        $this->_strTablePrefix = $objBaseModel->_strTablePrefix;
        $this->_intActiveTime = 120;
    }


    public function checkUserDataExists($strUsername, $strEmail) {
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


    public function createNewUser($strUsername, $strPassword, $strEmail) {

        $strNewUserStatement = "INSERT INTO " . $this->_strTablePrefix . "users ( `user_name`, `email`, `last_attempt`, "
            . "`last_active`, `password`, `salt`, `ip_address`, `reset_token` ) VALUES ( :uname, :email, :time, :time, "
            . ":pass, :salt, :ip, :token )";

        $objNewUserPDO = $this->_objPDO->prepare($strNewUserStatement);

        $strSalt = HashHelper::createSalt(20);
        $strHashedPassword = HashHelper::makeHash($strPassword, $strSalt);
        $strToken = HashHelper::getToken($strHashedPassword);
        $intClientIpLong =  $this->getClientIpLong();
        $intTimestamp = time();

        $objNewUserPDO->bindValue(':uname', $strUsername, PDO::PARAM_STR);
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

    public function resetUserPassword($strNewPassword, $strEmail, $strOldToken) {
        $strResetStatement = "UPDATE " . $this->_strTablePrefix . "users SET `password` = :pass , "
            . "`reset_token` = :newtoken, `last_attempt` = :time, `ip_address` = :ip, `salt` = :salt "
            . "WHERE `email` LIKE :email AND `reset_token` LIKE :oldtoken";

        $objNewPasswordPDO = $this->_objPDO->prepare($strResetStatement);

        $strSalt = HashHelper::createSalt(20);
        $strHashedPassword = HashHelper::makeHash($strNewPassword, $strSalt);
        $strToken = HashHelper::getToken($strHashedPassword);
        $intClientIpLong =  $this->getClientIpLong();
        $intTimestamp = time();

        $objNewPasswordPDO->bindValue(':pass', $strHashedPassword, PDO::PARAM_STR);
        $objNewPasswordPDO->bindValue(':newtoken', $strToken, PDO::PARAM_STR);
        $objNewPasswordPDO->bindValue(':email', $strEmail, PDO::PARAM_STR);
        $objNewPasswordPDO->bindValue(':ip', $intClientIpLong, PDO::PARAM_INT);
        $objNewPasswordPDO->bindValue(':salt', $strSalt, PDO::PARAM_STR);
        $objNewPasswordPDO->bindValue(':time', $intTimestamp, PDO::PARAM_INT);
        $objNewPasswordPDO->bindValue(':oldtoken', $strOldToken, PDO::PARAM_STR);
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

        $strHashedPassword = HashHelper::makeHash($strPassword, $strSalt);

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

    public function setUserActive($intUserId) {
        $intTimestamp = time();
        $strResetStatement = "UPDATE " . $this->_strTablePrefix . "users SET `last_active` = :time "
            . "WHERE `id` = :userid";

        $objResetAttemptsPDO = $this->_objPDO->prepare($strResetStatement);
        $objResetAttemptsPDO->bindValue(':userid', $intUserId, PDO::PARAM_INT);
        $objResetAttemptsPDO->bindValue(':time', $intTimestamp, PDO::PARAM_INT);
        if($objResetAttemptsPDO->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getOnlineUserList() {
        $intTimeBuffer = time() - $this->_intActiveTime;
        $arrUserList = array();
        $strOnlineUserStatement = "SELECT id, user_name FROM " . $this->_strTablePrefix ."users WHERE last_active < :time";
        $objOnlineUserPDO = $this->_objPDO->prepare($strOnlineUserStatement);
        $objOnlineUserPDO->bindValue(':time', $intTimeBuffer, PDO::PARAM_INT);
        if($objOnlineUserPDO->execute()) {
            $arrUserList = $objOnlineUserPDO->fetchAll(PDO::FETCH_ASSOC);
        }
        return $arrUserList;

    }

}

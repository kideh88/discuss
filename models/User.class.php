<?php

class UserModel extends BaseModel {

    private $_intBlockedTime;

    public function __construct() {
        $this->_intBlockedTime = 300;


    }


    private function checkUserDataExists($strUsername, $strEmail) {
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


    private function _setFailedAttempt($strUsername, $intClientIpLong, $intAttempts) {
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


}

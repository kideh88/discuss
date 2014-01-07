<?php


class UserController extends BaseController {

    private $_objUserModel;

    public function __construct() {
        $this->_objBaseModel->requireModel('User');
        $this->_objUserModel = new UserModel();
    }


    public function test() {
        return parent::test();
    }


    public function doUserLogin($arrParameters) {
        $strUsername = $arrParameters['strUsername'];
        $strPassword = $arrParameters['strPassword'];

        $arrLoginData = $this->_objUserModel->getUserLoginData($strUsername);
        if(!$arrLoginData) {
            return false;
        }
        $intAttempts = (int)$arrLoginData['failed_attempts'];
        $intLastIpLong = (int)$arrLoginData['ip_address'];
        $intLastAttemptTime = (int)$arrLoginData['last_attempt'];
        $blnUserIsBlocked = $this->_objUserModel->checkUserIsBlocked($intAttempts, $intLastIpLong, $intLastAttemptTime);
        if($blnUserIsBlocked){
            return false;
        }
        $strSalt = $arrLoginData['salt'];
        $arrSessionData = $this->_objUserModel->userLoggedIn($strUsername, $strSalt, $strPassword);
        if(!$arrSessionData){
            $intUserIpLong = $this->_objUserModel->getClientIpLong();
            $this->_objUserModel->setFailedAttempt($strUsername, $intUserIpLong, $intAttempts);
            return false;
        }
        $this->_objUserModel->resetLoginAttempts($arrSessionData['intUserId']);
        SessionHelper::setSessionValues($arrSessionData);



    }
}
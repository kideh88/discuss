<?php


class UserController extends BaseController {

    private $_objUserModel;

    public function __construct() {
        parent::__construct();
        $this->_objBaseModel->requireModel('User');
        $this->_objUserModel = new UserModel($this->_objBaseModel);
    }

    public function doUserLogin($arrParameters) {
        $arrResponse = array(
            'success' => false
        );
        $strUsername = $arrParameters['strUsername'];
        $strPassword = $arrParameters['strPassword'];

        $arrLoginData = $this->_objUserModel->getUserLoginData($strUsername);
        if(!$arrLoginData) {
            $arrResponse['intFeedbackCode'] = 5;
            return $arrResponse;
        }
        $intAttempts = (int)$arrLoginData['failed_attempts'];
        $intLastIpLong = (int)$arrLoginData['ip_address'];
        $intLastAttemptTime = (int)$arrLoginData['last_attempt'];
        $blnUserIsBlocked = $this->_objUserModel->checkUserIsBlocked($intAttempts, $intLastIpLong, $intLastAttemptTime);
        if($blnUserIsBlocked){
            $arrResponse['intFeedbackCode'] = 8;
            return $arrResponse;
        }
        $strSalt = $arrLoginData['salt'];
        $arrSessionData = $this->_objUserModel->userLoggedIn($strUsername, $strSalt, $strPassword);
        if(!$arrSessionData){
            $intUserIpLong = $this->_objUserModel->getClientIpLong();
            $this->_objUserModel->setFailedAttempt($strUsername, $intUserIpLong, $intAttempts);
            $blnBlock = (0 === 3-($intAttempts+1));
            $arrResponse['intFeedbackCode'] = ($blnBlock ? 9 : 10);
            if(!$blnBlock) {
                $arrResponse['attempts'] = 3-($intAttempts+1);
            }
            return $arrResponse;
        }
        $this->_objUserModel->resetLoginAttempts($arrSessionData['intUserId']);
        $this->_objUserModel->setUserActive($arrSessionData['intUserId']);
        SessionHelper::setSessionValues($arrSessionData);

        $arrResponse['success'] = true;
        return $arrResponse;
    }

    public function doUserRegister($arrParameters) {
        $arrResponse = array(
            'success' => false
        );
        $strEmail = $arrParameters['strEmail'];
        $strUsername = $arrParameters['strUsername'];
        $strPassword = $arrParameters['strPassword'];
        $strConfirmPassword = $arrParameters['strConfirmPassword'];

        if($this->_objUserModel->checkUserDataExists($strUsername, $strEmail)) {
            $arrResponse['intFeedbackCode'] = 4;
            return $arrResponse;
        }

        // Insert password strength test here - return false if it fails!

        if($strPassword !== $strConfirmPassword) {
            $arrResponse['intFeedbackCode'] = 11;
            return $arrResponse;
        }

        if(!$this->_objUserModel->createNewUser($strUsername, $strPassword, $strEmail)) {
            $arrResponse['intFeedbackCode'] = 12;
            return $arrResponse;
        }

        // Email part here

        $arrResponse['success'] = true;
        $arrResponse['intFeedbackCode'] = 13;
        return $arrResponse;

    }

    public function doUserLogout() {
        $arrResponse['success'] = $this->_objUserModel->userLoggedOut();
        return $arrResponse;
    }

    public function getOnlineUsers() {
        $arrUsers = $this->_objUserModel->getOnlineUserList();
        return $arrUsers;
    }

}
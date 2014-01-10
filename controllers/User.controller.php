<?php


class UserController extends BaseController {

    public $_objUserModel;

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
        if(true === $arrParameters['blnRemember']) {
            $strCookieHash = HashHelper::createSalt(20) . time();
            CookieHelper::put('discuss', $strCookieHash);
            $this->_objUserModel->setCookieHash($arrSessionData['intUserId'], $strCookieHash);
        }

        $arrResponse['success'] = true;
        return $arrResponse;
    }

    public function doUserCookieLogin($strCookieHash) {
        $intUserId = (int)$this->_objUserModel->getCookieUser($strCookieHash);
        if(0 === $intUserId) {
            return false;
        }
        $strUsername = $this->_objUserModel->getUsernameById($intUserId);
        $blnIsAdmin = $this->_objUserModel->checkUserIsAdmin($intUserId);
        $arrSessionData['blnLoggedIn'] = true;
        $arrSessionData['intUserId'] = $intUserId;
        $arrSessionData['blnIsAdmin'] = $blnIsAdmin;
        $arrSessionData['strUsername'] = $strUsername;

        SessionHelper::setSessionValues($arrSessionData);

        return true;

    }

    public function doUserRegister($arrParameters) {
        $arrResponse = array(
            'success' => false
        );
        if(!UserInputHelper::checkSpecialCharacters($arrParameters['strUsername'])) {
            $arrResponse['intFeedbackCode'] = 19;
            return $arrResponse;
        }
        $strEmail = UserInputHelper::clean($arrParameters['strEmail']);
        $strUsername = $arrParameters['strUsername'];
        $strPassword = $arrParameters['strPassword'];
        $strConfirmPassword = $arrParameters['strConfirmPassword'];

        if($this->_objUserModel->checkUserDataExists($strUsername, $strEmail)) {
            $arrResponse['intFeedbackCode'] = 4;
            return $arrResponse;
        }

        if($strPassword !== $strConfirmPassword) {
            $arrResponse['intFeedbackCode'] = 11;
            return $arrResponse;
        }
        if(!HashHelper::checkPasswordStrength($strPassword)) {
            $arrResponse['intFeedbackCode'] = 11;
            return $arrResponse;
        }

        if(!$this->_objUserModel->createNewUser($strUsername, $strPassword, $strEmail)) {
            $arrResponse['intFeedbackCode'] = 12;
            return $arrResponse;
        }

        $arrResponse['success'] = true;
        $arrResponse['intFeedbackCode'] = 13;
        return $arrResponse;

    }

    public function doUserLogout() {
        $arrResponse['success'] = false;
        if($_SESSION['blnLoggedIn']) {
            $arrResponse['success'] = $this->_objUserModel->userLoggedOut();
            $this->_objUserModel->setUserInactive($_SESSION['intUserId']);
            CookieHelper::delete('discuss');
        }
        return $arrResponse;
    }

    public function doUpdateProfile($arrParameters) {
        $this->_objUserModel->setUserActive($_SESSION['intUserId']);
        $arrResponse['success'] = false;
        $arrResponse['intFeedbackCode'] = 17;
        if(!$_SESSION['blnLoggedIn']) {
            $arrResponse['intFeedbackCode'] = 15;
            return $arrResponse;
        }
        $intUserId = $_SESSION['intUserId'];

        $arrProfileData = $this->_objUserModel->getUserProfile($intUserId);
        if(!empty($_FILES) && $_FILES['image']['name'] !== '') {
            $arrUploadInfo = $this->_objSIU->safeSave();
            if($arrUploadInfo['success']) {
                if($arrUploadInfo['filename'] !== '') {
                    $arrProfileData['strProfileImage'] = $arrUploadInfo['filename'];
                }
                $arrResponse['strUploadError'] = $arrUploadInfo['error'];
            }
        }

        $strProfileText = $arrParameters['strText'];
        $arrResponse['success'] = $this->_objUserModel->updateUserProfile($intUserId, $arrProfileData['strProfileImage'], $strProfileText);
        if($arrResponse['success']) {
            $arrResponse['intFeedbackCode'] = 16;
        }

        return $arrResponse;
    }

    public function getOnlineUsers() {
        $arrUsers = $this->_objUserModel->getOnlineUserList();
        return $arrUsers;
    }

    public function getUserProfile($strUsername) {
        $intUserId = $this->_objUserModel->getIdByUsername($strUsername);
        $arrUserData = array(
            'strUsername' => 'Unknown'
            , 'strProfileImage' => 'default.jpg'
            , 'strProfileText' => FeedbackHelper::getMessage(14)
            , 'intUserId' => $intUserId
        );
        if(0 === $intUserId) {
            return $arrUserData;
        }
        else {
            $arrProfileData = $this->_objUserModel->getUserProfile($intUserId);
            $arrUserData = array_replace($arrUserData, $arrProfileData);
            $arrUserData['strUsername'] = $this->_objUserModel->getUsernameById($intUserId);
        }

        return $arrUserData;

    }

}
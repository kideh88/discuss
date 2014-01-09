<?php


class ChatController extends BaseController {

    public $_objUserModel;
    public $_objChatModel;

    public function __construct() {
        parent::__construct();
        $this->_objBaseModel->requireModel('User');
        $this->_objUserModel = new UserModel($this->_objBaseModel);
        $this->_objBaseModel->requireModel('Chat');
        $this->_objChatModel = new ChatModel($this->_objBaseModel);
    }


    public function doStartPublicChat($arrParameters) {
        $arrResponse = array(
            'success' => false
        );
        if(!$_SESSION['blnLoggedIn']) {
            $arrResponse['intFeedbackCode'] = 15;
            return $arrResponse;
        }
        $this->_objUserModel->setUserActive($_SESSION['intUserId']);
        $strRoomName = $arrParameters['strRoomName'];
        if(!UserInputHelper::checkSpecialCharacters($strRoomName)) {
            $arrResponse['intFeedbackCode'] = 20;
            return $arrResponse;
        }

        if($this->_objChatModel->checkDuplicateRoom($strRoomName)) {
            $arrResponse['intFeedbackCode'] = 21;
            return $arrResponse;
        }

        $intUserId = $_SESSION['intUserId'];
        $intNewRoomId = $this->_objChatModel->createChatRoom($intUserId, $strRoomName, true);
        if(!$intNewRoomId) {
            $arrResponse['intFeedbackCode'] = 22;
            return $arrResponse;
        }

        if(!$this->_objChatModel->setUserToRoom($intUserId, $intNewRoomId, 1)) {
            $arrResponse['intFeedbackCode'] = 23;
            return $arrResponse;
        }

        $arrResponse['success'] = true;

        return $arrResponse;
    }

    public function doChatInviteUser($arrParameters) {
        $arrResponse = array(
            'success' => false
        );
        $intUserId = $_SESSION['intUserId'];
        if(!$intUserId) {
            $arrResponse['intFeedbackCode'] = 15;
            return $arrResponse;
        }
        $this->_objUserModel->setUserActive($intUserId);
        $intChatRoomId = $arrParameters['intChatRoomId'];
        $strUsername = UserInputHelper::clean($arrParameters['strUsername']);

        $intInvitedId = $this->_objUserModel->getIdByUsername($strUsername);
        if(0 === $intInvitedId) {
            $arrResponse['intFeedbackCode'] = 5;
            return $arrResponse;
        }

        if(!$this->_objChatModel->checkChatAccess($intUserId, $intChatRoomId)) {
            $arrResponse['intFeedbackCode'] = 24;
            return $arrResponse;
        }
        if(!$this->_objChatModel->setUserToRoom($intInvitedId, $intChatRoomId, 2)) {
            $arrResponse['intFeedbackCode'] = 25;
            return $arrResponse;
        }

        $arrResponse['success'] = true;
        $arrResponse['intFeedbackCode'] = 26;
        return $arrResponse;
    }

    public function getPublicChatList() {
        $arrResponse = array(
            'success' => false
        );
        $arrPublicRoomData = $this->_objChatModel->getPublicRoomList();
        if(0 === count($arrPublicRoomData)) {
            $arrResponse['intFeedbackCode'] = 27;
            return $arrResponse;
        }
        foreach($arrPublicRoomData as $arrRoomData) {
            $arrRoomDetails = $this->_objChatModel->getChatRoomUsers($arrRoomData['intChatId']);
            $arrRoomData['intUsers'] = count($arrRoomDetails);
            $arrUserList = array_slice($arrRoomDetails, 0, 9);
            $strDots = ($arrRoomData['intUsers'] > 10 ? '...' : '');
            foreach($arrUserList as $arrUserData) {
                $arrRoomData['strUsers'] .= $arrUserData['strUsername'];
            }
            $arrRoomData['strUsers'] .= $strDots;
            $arrResponse['arrPublicChats'][] = $arrRoomData;

        }
        $arrResponse['success'] = true;
        return $arrResponse;

    }

}
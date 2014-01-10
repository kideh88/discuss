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

        if(0 < $this->_objChatModel->checkDuplicateRoom($strRoomName)) {
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
        $arrPublicRoomData = $this->_objChatModel->getChatRoomList(1);
        if(0 === count($arrPublicRoomData)) {
            $arrResponse['intFeedbackCode'] = 27;
            return $arrResponse;
        }
        foreach($arrPublicRoomData as $arrRoomData) {
            $arrRoomDetails = $this->_objChatModel->getChatRoomUsers($arrRoomData['intChatId']);
            $arrRoomData['intUsers'] = count($arrRoomDetails);
            $arrRoomData['blnIsMember'] = false;
            $arrUserList = array_slice($arrRoomDetails, 0, 9);
            $strDots = ($arrRoomData['intUsers'] > 10 ? '...' : '');
            foreach($arrUserList as $intKey => $arrUserData) {
                if($_SESSION['blnLoggedIn'] && $arrUserData['strUsername'] === $_SESSION['strUsername']) {
                    $arrRoomData['blnIsMember'] = true;
                }
                $arrRoomData['strUsers'] .= $arrUserData['strUsername'];
                $arrRoomData['strUsers'] .= ($intKey < count($arrUserList)-1 ? ', ' : '');
            }
            $arrRoomData['strUsers'] .= $strDots;
            $arrResponse['arrPublicChats'][] = $arrRoomData;
        }
        $arrResponse['success'] = true;
        return $arrResponse;

    }

    public function getPrivateChatList() {
        $arrPrivateRoomData = $this->_objChatModel->getChatRoomList(0);
        if(0 === count($arrPrivateRoomData)) {
            $arrResponse['intFeedbackCode'] = 31;
            return $arrResponse;
        }
        foreach($arrPrivateRoomData as $arrRoomData) {
            $arrRoomDetails = $this->_objChatModel->getChatRoomUsers($arrRoomData['intChatId']);
            $arrRoomData['intUsers'] = count($arrRoomDetails);
            $arrRoomData['blnIsMember'] = false;
            $arrUserList = array_slice($arrRoomDetails, 0, 9);
            $strDots = ($arrRoomData['intUsers'] > 10 ? '...' : '');
            foreach($arrUserList as $intKey => $arrUserData) {
                if($_SESSION['blnLoggedIn'] && $arrUserData['strUsername'] === $_SESSION['strUsername']) {
                    $arrRoomData['blnIsMember'] = true;
                }
                $arrRoomData['strUsers'] .= $arrUserData['strUsername'];
                $arrRoomData['strUsers'] .= ($intKey < count($arrUserList)-1 ? ', ' : '');
            }
            $arrRoomData['strUsers'] .= $strDots;
            $arrResponse['arrPrivateChats'][] = $arrRoomData;
        }
        $arrResponse['success'] = true;
        return $arrResponse;
    }


    public function updateChatLog($arrParameters) {
        $arrResponse = array(
            'success' => false
        );
        $intChatId = $arrParameters['intChatId'];
        $intLastMessageId = $arrParameters['intLastMessage'];
        $arrChatData = $this->checkValidChat($intChatId);
        if(!$arrChatData['success']) {
            return $arrChatData;
        }
        $arrChatMessages = $this->_objChatModel->getChatRoomMessages($intChatId, $intLastMessageId);
        if(!$arrChatMessages) {
            $arrResponse['intFeedbackCode'] = 29;
            return $arrResponse;
        }
        $arrResponse['arrChatMessages'] = $arrChatMessages;

        $arrResponse['success'] = true;
        return $arrResponse;
    }

    public function checkValidChat($intChatId) {
        $arrResponse = array(
            'success' => false
        );
        if(!$_SESSION['blnLoggedIn']) {
            $arrResponse['strMessage'] = FeedbackHelper::getMessage(15);
            return $arrResponse;
        }
        $arrChatRoomData = $this->_objChatModel->getChatRoomData($intChatId);
        if(!$arrChatRoomData || $arrChatRoomData['is_closed']) {
            $arrResponse['strMessage'] = FeedbackHelper::getMessage(28);
            return $arrResponse;
        }

        $intUserId = $_SESSION['intUserId'];
        if(!$arrChatRoomData['is_public']) {
            if(!$this->_objChatModel->checkChatAccess($intUserId, $intChatId)) {
                $arrResponse['strMessage'] = FeedbackHelper::getMessage(24);
                return $arrResponse;
            }
        }

        if(!$this->_objChatModel->checkChatAccess($intUserId, $intChatId)) {
            if(!$this->_objChatModel->setUserToRoom($intUserId, $intChatId, 2)) {
                $arrResponse['strMessage'] = FeedbackHelper::getMessage(23);
                return $arrResponse;
            }
        }

        $arrResponse['success'] = true;
        return $arrResponse;

    }

    public function doLeaveRoom($arrParameters) {
        $arrResponse = array(
            'success' => false
        );
        if(!$_SESSION['blnLoggedIn']) {
            $arrResponse['strMessage'] = FeedbackHelper::getMessage(15);
            return $arrResponse;
        }
        $intUserId = $_SESSION['intUserId'];
        $intChatId = $arrParameters['intChatId'];

        $arrResponse['success'] = $this->_objChatModel->setUserLeftRoom($intUserId, $intChatId);
        return $arrResponse;
    }

    public function sendChatMessage($arrParameters) {
        $arrResponse = array(
            'success' => false
        );
        if(!$_SESSION['blnLoggedIn']) {
            $arrResponse['intFeedbackCode'] = 15;
            return $arrResponse;
        }

        $intUserId = $_SESSION['intUserId'];
        $intChatId = (int)$arrParameters['strChatId'];
        $arrChatData = $this->checkValidChat($intChatId);
        if(!$arrChatData['success']) {
            return $arrChatData;
        }
        $this->_objUserModel->setUserActive($intUserId);

        $strCleanedMessage = UserInputHelper::cleanUserInput($arrParameters['strChatMessage'], 'chat');
        if(!$this->_objChatModel->insertNewChatMessage($intUserId, $intChatId, $strCleanedMessage, time())) {
            $arrResponse['intFeedbackCode'] = 30;
            return $arrResponse;
        }

        $arrResponse['success'] = true;
        return $arrResponse;

    }

    public function doJoinPrivateRoom($arrParameters) {
        $arrResponse = array(
            'success' => false
        );
        if(!$_SESSION['blnLoggedIn']) {
            $arrResponse['intFeedbackCode'] = 15;
            return $arrResponse;
        }

        $intUserId = $arrParameters['intUserId'];
        $strUsername = $this->_objUserModel->getUsernameById($intUserId);
        if('' === $strUsername) {
            $arrResponse['intFeedbackCode'] = 5;
            return $arrResponse;
        }

        $strCurrentUsername = $this->_objUserModel->getUsernameById($_SESSION['intUserId']);
        $strRoomName = $strCurrentUsername . '-' . $strUsername;

        $intChatId = $this->_objChatModel->checkDuplicateRoom($strRoomName);
        if(0 < $intChatId) {
            $arrResponse['success'] = true;
            $arrResponse['intChatId'] = $intChatId;
            return $arrResponse;
        }

        $intNewRoomId = $this->_objChatModel->createChatRoom($_SESSION['intUserId'], $strRoomName, 0);
        if(!$intNewRoomId) {
            $arrResponse['intFeedbackCode'] = 22;
            return $arrResponse;
        }

        if(!$this->_objChatModel->setUserToRoom($_SESSION['intUserId'], $intNewRoomId, 1)
            || !$this->_objChatModel->setUserToRoom($intUserId, $intNewRoomId, 2)
        ) {
            $arrResponse['intFeedbackCode'] = 23;
            return $arrResponse;
        }

        $arrResponse['success'] = true;
        $arrResponse['intChatId'] = $intNewRoomId;
        return $arrResponse;

    }




}
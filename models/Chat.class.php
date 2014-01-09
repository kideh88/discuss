<?php


class ChatModel extends BaseModel {

    protected $_strTablePrefix;
    protected $_objPDO;
//    private $_objUserModel;

    public function __construct(BaseModel &$objBaseModel) {
        $this->_objPDO = $objBaseModel->_objPDO;
        $this->_strTablePrefix = $objBaseModel->_strTablePrefix;

//        $objBaseModel->requireModel('User');
//        $this->_objUserModel = new UserModel($objBaseModel);
    }

    public function createChatRoom($intUserId, $strRoomName, $blnPublicRoom) {
        $strNewChatStatement = "INSERT INTO " . $this->_strTablePrefix . "chat_rooms ( `name`, `is_public`, "
            . "`users_fk` ) VALUES ( :name, :public, :userid )";

        $objNewChatPDO = $this->_objPDO->prepare($strNewChatStatement);

        $objNewChatPDO->bindValue(':name', $strRoomName, PDO::PARAM_STR);
        $objNewChatPDO->bindValue(':public', $blnPublicRoom, PDO::PARAM_BOOL);
        $objNewChatPDO->bindValue(':userid', $intUserId, PDO::PARAM_INT);

        if($objNewChatPDO->execute()) {
            return $this->_objPDO->lastInsertId();
        }
        else{
            return false;
        }


    }

    public function checkDuplicateRoom($strRoomName) {
        $intExisting = 0;

        $strCheckExistingStatement = "SELECT COUNT(id) FROM " . $this->_strTablePrefix . "chat_rooms WHERE name "
            . "LIKE :name AND is_closed = 0 ";
        $objExistUserPDO = $this->_objPDO->prepare($strCheckExistingStatement);
        $objExistUserPDO->bindValue(':name', $strRoomName, PDO::PARAM_STR);
        if($objExistUserPDO->execute()) {
            $intExisting = $objExistUserPDO->fetchColumn();
        }
        return (0 < $intExisting);
    }

    public function getOpenPrivateChat($intUserId, $intInvitedId) {
        $intExisting = 0;
        $strPrivateExistingStatement = "SELECT rooms.id FROM " . $this->_strTablePrefix . "chat_rooms as rooms "
            . "LEFT JOIN " . $this->_strTablePrefix . "chat_members as members ON rooms.id = members.chat_rooms_fk "
            . "WHERE ( rooms.users_fk = :userid AND members.users_fk = :inviteid ) OR "
            . "( rooms.users_fk = :inviteid AND members.users_fk = :userid ) AND rooms.is_public = 0 AND rooms.is_closed = 0 ";
        $objPrivateChatPDO = $this->_objPDO->prepare($strPrivateExistingStatement);
        $objPrivateChatPDO->bindValue(':userid', $intUserId, PDO::PARAM_INT);
        $objPrivateChatPDO->bindValue(':inviteid', $intInvitedId, PDO::PARAM_INT);
        if($objPrivateChatPDO->execute()) {
            $intExisting = $objPrivateChatPDO->fetchColumn();
        }
        return $intExisting;
    }

    public function checkChatAccess($intUserId, $intChatId) {
        $intExisting = 0;
        $strCheckExistingStatement = "SELECT COUNT(users_fk) FROM " . $this->_strTablePrefix . "chat_members "
            . "WHERE chat_rooms_fk = :chatid AND users_fk = :userid";
        $objExistUserPDO = $this->_objPDO->prepare($strCheckExistingStatement);
        $objExistUserPDO->bindValue(':userid', $intUserId, PDO::PARAM_INT);
        $objExistUserPDO->bindValue(':chatid', $intChatId, PDO::PARAM_INT);
        if($objExistUserPDO->execute()) {
            $intExisting = $objExistUserPDO->fetchColumn();
        }
        return (0 < $intExisting);
    }

    public function setUserToRoom($intUserId, $intChatId, $intUserRole) {
        $strNewChatStatement = "INSERT INTO " . $this->_strTablePrefix . "chat_members ( `chat_rooms_fk`, "
            . "`users_fk`, `user_roles_fk` ) VALUES ( :roomid, :userid, :roleid )";

        $objNewChatPDO = $this->_objPDO->prepare($strNewChatStatement);

        $objNewChatPDO->bindValue(':roomid', $intChatId, PDO::PARAM_INT);
        $objNewChatPDO->bindValue(':userid', $intUserId, PDO::PARAM_INT);
        $objNewChatPDO->bindValue(':roleid', $intUserRole, PDO::PARAM_INT);

        if($objNewChatPDO->execute()) {
            return true;
        }
        else{
            return false;
        }

    }

    public function getPublicRoomList() {
        $strPublicChatsStatement = "SELECT id as intChatId , name as strRoomName FROM " . $this->_strTablePrefix . "chat_rooms "
            . "WHERE is_public = 1 AND is_closed = 0";
        $objPublicChatsPDO = $this->_objPDO->prepare($strPublicChatsStatement);
        if($objPublicChatsPDO->execute()) {
            $arrPublicChatsData = $objPublicChatsPDO->fetchAll(PDO::FETCH_ASSOC);
            return $arrPublicChatsData;
        }
        return false;
    }

    public function getChatRoomUsers($intChatId) {
        $strChatUsersStatement = "SELECT users.user_name as strUsername FROM " . $this->_strTablePrefix . "chat_members as members "
            . "LEFT JOIN " . $this->_strTablePrefix . "users as users ON members.users_fk = users.id "
            . "WHERE members.chat_rooms_fk = :chatid ";
        $objChatUsersPDO = $this->_objPDO->prepare($strChatUsersStatement);

        $objChatUsersPDO->bindValue(':chatid', $intChatId, PDO::PARAM_INT);
        if($objChatUsersPDO->execute()) {
            $arrChatUsers = $objChatUsersPDO->fetchAll(PDO::FETCH_ASSOC);
            return $arrChatUsers;
        }
        return false;
    }

    public function setUserLeftRoom($intUserId, $intChatId) {

        $this->_setChatRoomClosed($intChatId);

    }

    private function _setChatRoomClosed($intChatId) {
        // close room if empty
    }
}
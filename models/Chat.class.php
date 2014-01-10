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

        $strCheckExistingStatement = "SELECT id FROM " . $this->_strTablePrefix . "chat_rooms WHERE name "
            . "LIKE :name AND is_closed = 0 ";
        $objExistUserPDO = $this->_objPDO->prepare($strCheckExistingStatement);
        $objExistUserPDO->bindValue(':name', $strRoomName, PDO::PARAM_STR);
        if($objExistUserPDO->execute()) {
            $intExisting = $objExistUserPDO->fetchColumn();
        }
        return $intExisting;
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

    public function getChatRoomList($blnIsPublic) {
        $strPublicChatsStatement = "SELECT id as intChatId , name as strRoomName FROM " . $this->_strTablePrefix . "chat_rooms "
            . "WHERE is_public = :public AND is_closed = 0";
        $objPublicChatsPDO = $this->_objPDO->prepare($strPublicChatsStatement);
        $objPublicChatsPDO->bindValue(':public', $blnIsPublic, PDO::PARAM_INT);
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
        $strLeaveStatement = "DELETE FROM " . $this->_strTablePrefix . "chat_members "
            . "WHERE users_fk = :userid AND chat_rooms_fk = :chatid ";
        $objLeaveRoomPDO = $this->_objPDO->prepare($strLeaveStatement);
        $objLeaveRoomPDO->bindValue(':userid', $intUserId, PDO::PARAM_INT);
        $objLeaveRoomPDO->bindValue(':chatid', $intChatId, PDO::PARAM_INT);
        if($objLeaveRoomPDO->execute()) {
            $this->_setChatRoomClosed($intChatId);
            return true;
        }
        return false;

    }

    private function _setChatRoomClosed($intChatId) {
        $strCheckMembersStatement = "SELECT COUNT(users_fk) FROM " . $this->_strTablePrefix . "chat_members "
            . "WHERE chat_rooms_fk = :chatid";
        $objMembersPDO = $this->_objPDO->prepare($strCheckMembersStatement);
        $objMembersPDO->bindValue(':chatid', $intChatId, PDO::PARAM_INT);
        if($objMembersPDO->execute()) {
            $intExisting = $objMembersPDO->fetchColumn();
            if($intExisting > 0) {
                return true;
            }
            else {
                $strCloseRoomStatement = "DELETE FROM " . $this->_strTablePrefix . "chat_rooms "
                    . "WHERE id = :chatid ";
                $objCloseRoomPDO = $this->_objPDO->prepare($strCloseRoomStatement);
                $objCloseRoomPDO->bindValue(':chatid', $intChatId, PDO::PARAM_INT);
                if($objCloseRoomPDO->execute()) {
                    return true;
                }
            }
        }
        return false;

    }

    public function getChatRoomData($intChatId) {
        $strChatDataStatement = "SELECT name, is_public, is_closed FROM " . $this->_strTablePrefix . "chat_rooms "
            . "WHERE id = :chatid";
        $objChatDataPDO = $this->_objPDO->prepare($strChatDataStatement);
        $objChatDataPDO->bindValue(':chatid', $intChatId, PDO::PARAM_INT);
        if($objChatDataPDO->execute()) {
            $arrChatData = $objChatDataPDO->fetch(PDO::FETCH_ASSOC);
            return $arrChatData;
        }
        return false;
    }

    public function getChatRoomMessages($intChatId, $intLastMessageId) {
        $strChatStatement = "SELECT messages.id as intMessageId, messages.message as strMessage, messages.time as "
            . "intTime, users.user_name as strUsername FROM " . $this->_strTablePrefix . "chat_messages as messages "
            . "LEFT JOIN " . $this->_strTablePrefix . "users as users ON messages.users_fk = users.id "
            . "WHERE chat_rooms_fk = :chatid";
        if(0 < $intLastMessageId) {
            $strChatStatement .= " AND messages.id > :lastid ";
        }
        $objChatMessagesPDO = $this->_objPDO->prepare($strChatStatement);
        $objChatMessagesPDO->bindValue(':chatid', $intChatId, PDO::PARAM_INT);
        if(0 < $intLastMessageId) {
            $objChatMessagesPDO->bindValue(':lastid', $intLastMessageId, PDO::PARAM_INT);
        }
        if($objChatMessagesPDO->execute()) {
            $arrChatMessages = $objChatMessagesPDO->fetchAll(PDO::FETCH_ASSOC);
            return $arrChatMessages;
        }
        return false;
    }

    public function insertNewChatMessage($intUserId, $intChatId, $strMessage, $intTimestamp){
        $strNewMessageStatement = "INSERT INTO " . $this->_strTablePrefix . "chat_messages (`chat_rooms_fk`, "
            . "`message`, `time`, `users_fk` ) VALUES ( :chatid, :msg, :time, :userid )";

        $objChatMessagePDO = $this->_objPDO->prepare($strNewMessageStatement);
        $objChatMessagePDO->bindValue(':chatid', $intChatId, PDO::PARAM_INT);
        $objChatMessagePDO->bindValue(':msg', $strMessage, PDO::PARAM_STR);
        $objChatMessagePDO->bindValue(':time', $intTimestamp, PDO::PARAM_INT);
        $objChatMessagePDO->bindValue(':userid', $intUserId, PDO::PARAM_INT);
        if($objChatMessagePDO->execute()) {
            return true;
        }
        return false;

    }


}
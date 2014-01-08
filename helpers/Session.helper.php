<?php

class SessionHelper {

    public static function startSession() {
        $arrDefaultSettings = array(
            'blnLoggedIn' => false
            , 'intUserId' => 0
            , 'blnIsAdmin' => false
            , 'strUsername' => ''
        );
        if(session_id() === '' || !isset($_SESSION)) {
            session_start();
            foreach($arrDefaultSettings as $strKey => $mixValue) {
                if(!array_key_exists($strKey, $_SESSION)) {
                    $_SESSION[$strKey] = $mixValue;
                }
            }
        }

    }

    public static function setSessionValues($arrSessionData) {
        foreach($arrSessionData as $strKey => $mixValue) {
            $_SESSION[$strKey] = $mixValue;
        }
    }

}
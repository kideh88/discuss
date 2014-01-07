<?php

class SessionHelper {

    public static function startSession() {
        $arrDefaultSettings = array(
            'blnLoggedIn' => false
            , 'intUserId' => 0
            , 'blnIsAdmin' => false
            , 'strUsername' => ''
        );
        if(!isset($_SESSION)) {
            session_start();
            foreach($arrDefaultSettings as $strKey => $mixValue) {
                $_SESSION[$strKey] = $mixValue;
            }
        }

    }

    public static function setSessionValues($arrSessionData) {
        foreach($arrSessionData as $strKey => $mixValue) {
            $_SESSION[$strKey] = $mixValue;
        }
    }

}
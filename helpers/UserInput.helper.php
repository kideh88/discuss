<?php

class UserInputHelper {

    // function is overkill and should be modified according to the user input

    public static function clean($strInput) {
        $strInput = htmlspecialchars($strInput, ENT_IGNORE, 'utf-8');
        $strInput = strip_tags($strInput);
        $strInput = stripslashes($strInput);
        return $strInput;
    }

    public static function checkSpecialCharacters($strInput) {
        $strPattern = '/^[a-zA-Z0-9_ ]*$/';
        preg_match($strPattern, $strInput, $arrMatch);
        if(!$arrMatch) {
            return false;
        }
        else {
            return true;
        }
    }

}

<?php

class HashHelper {

    public static function makeHash($strPassword, $strSalt) {
        for ($intI = 0; $intI < 5; $intI += 1) {
            $strPassword = hash('sha256', $strPassword . $strSalt);
        }
        return $strPassword;
    }

    public static function getToken($strHashedPassword) {
        return substr($strHashedPassword, 20, 10);
    }

    public static function createSalt($intLength) {
        if(!is_int($intLength)) {
            return false;
        }
        $strSaltCharacters = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?$#=@';
        $strSalt = '';
        for($intI = 0; $intI < $intLength; $intI += 1) {
            $intLoopRandom = mt_rand(0, strlen($strSaltCharacters)-1);
            $strSalt .=  substr($strSaltCharacters, $intLoopRandom, 1);
        }
        return $strSalt;
    }

}

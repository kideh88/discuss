<?php

class CookieHelper {

    public static function exists($strName) {
        return(isset($_COOKIE[$strName]));
    }

    public static function get($strName) {
        return $_COOKIE[$strName];
    }

    public static function put($strName, $mixValue, $intExpire = 604800) {
        if (setcookie($strName, $mixValue, time() + $intExpire, '/')) {
            return true;
        }
        return false;
    }

    public static function delete($strName) {
        self::put($strName, '', time() - 1);
    }

}

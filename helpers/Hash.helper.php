<?php

class HashHelper {

    public static function makeHash($input, $salt, $rounds) {
        for ($i = 0; $i < $rounds; $i++) {
            $input = hash('sha256', $input . $salt);
        }

        return $input;
    }

    public static function salt($length) {
        return mcrypt_create_iv($length);
    }

}

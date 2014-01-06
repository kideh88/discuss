<?php

class UserInputHelper {

    // function is overkill and should be modified according to the user input

    public static function clean($input) {
        $input = mysql_real_escape_string($input);
        $input = htmlspecialchars($input, ENT_IGNORE, 'utf-8');
        $input = strip_tags($input);
        $input = stripslashes($input);
        return $input;
    }

}

<?php

class FeedbackHelper {

    private static $_arrUserFeedback = array(
        1 => 'Notice: Please login to join or create chats'
        , 2 => 'Error: Something went wrong! Please try again.'
        , 3 => 'Notice: Your password has been changed'
        , 4 => 'Error: Username or email already exists'
        , 5 => 'Error: User does not exists'
        , 6 => 'Error: That user is not online'
        , 7 => 'Error: You can´t invite yourself'
        , 8 => 'Notice: You are currently blocked, try again later'
        , 9 => 'Notice: You are now blocked for 5 minutes'
        , 10 => 'Notice: Wrong Password! Attempts before block: '
        , 11 => 'Error: Password confirmation did not match'
        , 12 => 'Error: Could not create user, please try again!'
        , 13 => 'Success: You are now registered!'
        , 14 => 'Error: The user profile you are trying to access does not exist! Check if the name is typed correctly'
        , 15 => 'Error: Cannot access method without login'
        , 16 => 'Notice: Your profile is now updated'
        , 17 => 'Error: Could not update profile'
        , 18 => 'Unknown error!'
    );

    public static function getMessage($intCode) {
        $arrFeedback = &FeedbackHelper::$_arrUserFeedback;
        if(array_key_exists($intCode, $arrFeedback)) {
            return $arrFeedback[$intCode];
        }
        else {
            return $arrFeedback[18];
        }

    }

}
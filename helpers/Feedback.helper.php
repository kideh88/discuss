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
        , 19 => 'Error: Special characters not allowed in username'
        , 20 => 'Error: Special characters not allowed in room name'
        , 21 => 'Error: Chat room with this name already exists'
        , 22 => 'Error: Could not create chat room, please try again'
        , 23 => 'Error: Could not join chat room, please try again'
        , 24 => 'Error: You are not a member of this chat room'
        , 25 => 'Error: Could not invite user to chat room, please try again'
        , 26 => 'Notice: User has been invited'
        , 27 => 'Notice: No Public chats were found'
        , 28 => 'Error: Chat room is closed or does not exist'
        , 29 => 'Notice: No newer chat messages'
        , 30 => 'Error: Could not send chat message'
        , 31 => 'Notice: No Private chats were found'
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
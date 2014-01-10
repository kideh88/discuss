<?php

class UserInputHelper {

    private static $_allowedProfileTags = array(
        "<p>",
        "<strong>",
        "<ol>",
        "<li>",
        "<table>",
        "<tbody>",
        "<tr>",
        "<td>",
        "<br />",
        "<br>",
        "<img>",
        "<a>",
        "<em>",
        "<span>",
        "<sup>",
        "<ul>",
        "<blockqoute>",
        "<hr>",
        "<hr />",
        "<code>"
    );
    private static $_allowedChatTags = array(
        "<strong>",
        "<em>",
        "<span>"
    );

    public static function cleanUserInput($strInput, $strType) {
        if ($strType === 'profile') {
            $_allowedTags = implode(",", self::$_allowedProfileTags);
        }
        else if ($strType === 'chat') {
            $_allowedTags = implode(",", self::$_allowedChatTags);
        }
        $strStrippedHtmlContent = strip_tags($strInput, $_allowedTags);
        return $strStrippedHtmlContent;
    }

    public static function clean($strInput) {
        $strInput = htmlspecialchars($strInput);
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

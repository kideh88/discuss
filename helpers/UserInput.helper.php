<?php

class UserInputHelper {

// function is overkill and should be modified according to the user input

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

    public static function cleanUserInput($input, $type) {
        if ($type == 'profile') {
            $_allowedTags = implode(",", self::$_allowedProfileTags);
        } elseif ($type == 'chat') {
            $_allowedTags = implode(",", self::$_allowedChatTags);
        }
        $strippedHtmlContent = strip_tags($input, $_allowedTags);

        if ($strippedHtmlContent === $input) {
            return $strippedHtmlContent;
        } else {
            self::cleanUserInput($strippedHtmlContent, $type);
        }
    }

}

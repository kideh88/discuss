<?php
if(isset($_POST) && array_key_exists('jsonString', $_POST)) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/discuss/controllers/Base.controller.php');
    $objBaseController = new BaseController($_POST);
}
else {
    $arrResponse = array(
        'error' => true
        , 'message' => 'No POST data'
    );
    return json_encode($arrResponse);
}
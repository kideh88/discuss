<?php
if(isset($_POST) && array_key_exists('jsonString', $_POST)) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/discuss/helpers/Session.helper.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/discuss/controllers/Base.controller.php');
    SessionHelper::startSession();
    $objBaseController = new BaseController($_POST['jsonString']);
}
else {
    $arrResponse = array(
        'success' => false
        , 'message' => 'No POST data'
    );
    return json_encode($arrResponse);
}
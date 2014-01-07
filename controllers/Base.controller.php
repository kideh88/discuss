<?php


// private only accessible from inside BASE
// protected can be accessed by extended Controllers (parent::method())

class BaseController {
    private $_strBasePath;
    protected $_objBaseModel;

    public function __construct($strPostRequest = null) {
        require_once($this->_strBasePath . '/helpers/Session.helper.php');

        $this->_strBasePath = $_SERVER['DOCUMENT_ROOT'] . '/discuss';
        global $objBaseModel;
        if(!is_object($objBaseModel)) {
            require_once($this->_strBasePath . '/models/Base.class.php');
            $objBaseModel = new BaseModel($this->_strBasePath);
        }
        $this->_objBaseModel = $objBaseModel;

        require_once($this->_strBasePath . '/helpers/Access.helper.php');
        require_once($this->_strBasePath . '/config/Access.include.php');
        AccessHelper::setAccessArray($arrAccessPermissions);

        if(null !== $strPostRequest) {
            echo json_encode($this->_runRequest($strPostRequest));
        }

    }

    private function _runRequest($strPostRequest) {

        $arrAllowedRequest = AccessHelper::validateRequest($strPostRequest);
        if(!$arrAllowedRequest) {
            $arrResponse = array(
                'error' => true
                , 'message' => 'Request not allowed'
            );
            return json_encode($arrResponse);
        }
        $strController = $arrAllowedRequest['strControllerName'].'Controller';
        $this->_requireController($arrAllowedRequest['strControllerName']);
        $objClass = new $strController();
        $mixReturnData = $objClass->$arrAllowedRequest['strMethodName']($arrAllowedRequest['arrParameters']);
        $arrResponse = array(
            'error' => false
            , 'data' => $mixReturnData
        );
        return json_encode($arrResponse);

    }

    private function _requireController($strClassName) {
        if(!class_exists($strClassName)) {
            require_once($this->_strBasePath . '/controllers/' . $strClassName . '.controller.php');
        }
    }
}
<?php



class BaseController {
    private $_strBasePath;
    private $_objBaseModel;

    public function __construct($strPostRequest = null) {

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

    // private only accessible from inside BASE
    // protected can be accessed by extended Controllers (parent::method())
    public function test() {
//        return AccessHelper::validateParameters();
    }

    private function _runRequest($strPostRequest) {

        return AccessHelper::validateRequest($strPostRequest);

//        $objClass = new $strControllerName

    }

}
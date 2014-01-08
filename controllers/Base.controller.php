<?php



class BaseController {
    protected $_strBasePath;
    protected $_objBaseModel;

    public function __construct($strPostRequest = null) {
        $this->_strBasePath = $_SERVER['DOCUMENT_ROOT'] . '/discuss';

        global $objBaseModel;
        if(!is_object($objBaseModel) || !($objBaseModel instanceof BaseModel)) {
            require_once($this->_strBasePath . '/models/Base.class.php');
            $objBaseModel = new BaseModel($this->_strBasePath);
        }
        $this->_objBaseModel = $objBaseModel;

        require_once($this->_strBasePath . '/helpers/Access.helper.php');
        require_once($this->_strBasePath . '/config/Access.include.php');
        AccessHelper::setAccessArray($arrAccessPermissions);


        require_once($this->_strBasePath . '/config/UserFeedback.include.php');
        $this->_arrUserErrorMessages = $arrUserFeedback;
        if(null !== $strPostRequest) {
            echo json_encode($this->_runRequest($strPostRequest));
        }

    }

    private function _runRequest($strPostRequest) {

        $arrAllowedRequest = AccessHelper::validateRequest($strPostRequest);
        if(!$arrAllowedRequest) {
            $arrResponse = array(
                'success' => false
                , 'message' => 'Request not allowed'
            );
            return $arrResponse;
        }
        $strController = $arrAllowedRequest['strControllerName'].'Controller';
        $this->_requireController($arrAllowedRequest['strControllerName']);
        $objClass = new $strController($this);
        $arrReturnData = $objClass->$arrAllowedRequest['strMethodName']($arrAllowedRequest['arrParameters']);

        if(array_key_exists('intFeedbackCode', $arrReturnData)) {
            $intFeedbackCode = &$arrReturnData['intFeedbackCode'];
            if(array_key_exists($intFeedbackCode, $this->_arrUserErrorMessages)) {
                $arrReturnData['message'] = $this->_arrUserErrorMessages[$intFeedbackCode];
            }
            unset($intFeedbackCode);
        }
        return $arrReturnData;

    }

    public function _requireController($strClassName) {
        if(!class_exists($strClassName)) {
            require_once($this->_strBasePath . '/controllers/' . $strClassName . '.controller.php');
        }
    }
}
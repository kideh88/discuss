<?php

class Discuss {
    public $_strBasePath;
    public $_objDataClass;

    public function __construct() {
        $this->_strBasePath = $_SERVER['DOCUMENT_ROOT'] . '/discuss';

        require_once($this->_strBasePath . '/config/config.inc.php');
        $this->_objDataClass = new Data($arrConnectionConfig);

    }

}

<?php

class Discuss {
    public $_strBasePath;

    public function __construct() {
        $this->_strBasePath = $_SERVER['DOCUMENT_ROOT'] . '/discuss';
        require_once($this->_strBasePath . '/config/config.inc.php');
    }

}
<?php

class BaseModel {
    public $_strBasePath;
    private  $_objPDO;

    public function __construct($strProjectPath) {
        $this->_strBasePath = $strProjectPath;

        require_once($this->_strBasePath . '/config/DBConfig.include.php');
        require_once($this->_strBasePath . '/models/Data.class.php');
        $objDataClass = new Data($arrConnectionConfig);
        $this->_objPDO = $objDataClass->pdo();

    }



}

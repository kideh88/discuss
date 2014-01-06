<?php

class BaseModel {
    public $_strBasePath;
    protected $_objPDO;
    protected $_strTablePrefix;

    public function __construct($strProjectPath) {
        $this->_strBasePath = $strProjectPath;
        $this->_strTablePrefix = 'discuss_';

        require_once($this->_strBasePath . '/config/DBConfig.include.php');
        require_once($this->_strBasePath . '/models/Data.class.php');
        $objDataClass = new Data($arrConnectionConfig);
        if(is_object($objDataClass) && method_exists($objDataClass, 'pdo')) {
            $this->_objPDO = $objDataClass->pdo();
        }


    }



}

<?php
class Data {
    private $objPDO;

    public function __construct($arrConfig) {
        try {
            $objConnection = new PDO('mysql:host=' . $arrConfig['server'] . '; dbname=' . $arrConfig['name'], $arrConfig['user'], $arrConfig['password']);
            $objConnection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $objConnection->exec("SET CHARACTER SET utf8");
            $this->objPDO = $objConnection;
        }
        catch (PDOException $err) {
            return "Failed to construct PDO Connection";
        }
    }

    public function pdo() {
        return $this->objPDO;
    }

    public static function createSalt($intLength) {
        if(!is_int($intLength)) {
            return false;
        }
        $strSaltCharacters = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?$#=@';
        $strSalt = '';
        for($intI = 0; $intI < $intLength; $intI += 1) {
            $intLoopRandom = mt_rand(0, strlen($strSaltCharacters)-1);
            $strSalt .=  substr($strSaltCharacters, $intLoopRandom, 1);
        }
        return $strSalt;
    }

}
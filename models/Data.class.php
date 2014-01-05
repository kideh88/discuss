<?php
class Data {
    
    private $objPDO;

    public function __construct($arrConfig) {
        try {
            $objConnection = new PDO('mysql:host=' . $arrConfig['server'] . '; dbname=' . $arrConfig['name'], $arrConfig['user'], $arrConfig['password']);
            $objConnection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $objConnection->exec("SET CHARACTER SET utf8"); // return all sql requests as UTF-8
            $this->objPDO = $objConnection;
        }
        catch (PDOException $err) {
            return json_encode(array('success' => false, 'error' => "Failed to construct PDO Connection"));
        }
    }

    public function pdo() {
        return $this->objPDO;
    }

}
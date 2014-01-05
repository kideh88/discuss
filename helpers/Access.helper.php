<?php

class AccessHelper {
    private static $_arrAccess;

    public static function validateRequest($strPostRequest) {
        $arrRequest = json_decode($strPostRequest, true);
        $arrRequired = array('controller', 'method', 'parameters');
        if(count(array_intersect_key(array_flip($arrRequired), $arrRequest)) !== count($arrRequired)) {
            return false;
        }
        $strControllerName = $arrRequest['controller'];
        $strMethodName = $arrRequest['method'];
        $arrParameters = $arrRequest['parameters'];
        if(!self::validateController($strControllerName)) {
            return false;
        }
        if(!self::validateMethod($strControllerName, $strMethodName)) {
            return false;
        }
        if(!self::validateParameters($strControllerName, $strMethodName, $arrParameters)) {
            return false;
        }

        $arrRequest = array(
            'strControllerName' => $strControllerName
            , 'strMethodName' => $strMethodName
            , 'arrParameters' => $arrParameters
        );
        return $arrRequest;
    }

    private function validateController($strControllerName) {
        return array_key_exists($strControllerName, self::$_arrAccess);
    }

    private function validateMethod($strControllerName, $strMethodName) {
        $arrMethods = self::$_arrAccess[$strControllerName];
        return array_key_exists($strMethodName, $arrMethods);
    }

    private function validateParameters($strControllerName, $strMethodName, $arrParameters) {

        $arrAllowedParams = self::$_arrAccess[$strControllerName][$strMethodName];
        foreach($arrParameters as $strKey => $strValue) {
            $blnParamAllowed = false;
            foreach($arrAllowedParams as $strParamName => $strParamType) {
                if($strKey === $strParamName && gettype($strValue) === $strParamType) {
                    $blnParamAllowed = true;
                }
            }
            if(!$blnParamAllowed) {
                return false;
            }
        }
        return true;

    }


    public static function setAccessArray($arrConfig) {
        self::$_arrAccess = $arrConfig;
    }

}
<?php

class RFDevTools extends RFRPC {
    public function __getParamKey()
    {
        return "rfdt";
    }

    public function __getMethodList()
    {
        return array();
    }

    protected static $queryList = array();

    public static function RegisterQuery ($queryObj) {
        self::$queryList []= $queryObj;
    }

    public static function getQueries () {
        return self::$queryList;
    }

    protected static $dsList = array();

    public static function RegisterDataSource($queryObj) {
        self::$dsList[]= $queryObj;
    }

    public static function getDataSources() {
        return self::$dsList;
    }

    public static function getDiagnostics () {
        return array(
            'uname' => php_uname('a'),
            'sapi' => php_sapi_name(),
            'php_version' => phpversion()
        );
    }
}
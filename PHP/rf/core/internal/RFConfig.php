<?php

global $buildId;


class RFConfig {
    public static function set ($key, $value) {
        self::$config[$key] = $value;
    }

    public static function setValues ($values) {
        foreach($values as $key => $value)
        {
            self::$config[$key] = $value;
        }
    }

    public static function get ($key) {
        if(!isset(self::$config[$key])) {
            RFAssert::Exception("Unknown key - $key in config");
        }
        return self::$config[$key];
    }

    public static function isSetAndTrue ($key) {
        if(isset(self::$config[$key]))
            return self::$config[$key] === true;
        return false;
    }

    private static $config = array(
        'debug' => true,
        'rfdevel' => false,
        'theme' => 'silver',
        'webroot' => '/',
        'utf8_recode' => true, // internal. please don't change for now
        'default_timezone' => 'UTC'
    );
}

RFConfig::set('buildId', $buildId);

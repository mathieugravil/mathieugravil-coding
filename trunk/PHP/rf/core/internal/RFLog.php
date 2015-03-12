<?php
class RFLog {
    public static function log($msg, $obj = null) {
        if(RFConfig::isSetAndTrue('debug')) {
            self::pushMsg($msg, $obj);
        }
    }

    public static function debug ($msg, $obj = null) {

        if(RFConfig::isSetAndTrue('rfdevel')) {
            self::pushMsg($msg, $obj);
        }
    }

    protected static $profilerName;
    protected static $profilerStart;
    protected static $profilerSteps;

    public static function createProfiler($profilerName) {
        self::$profilerName = $profilerName;
        self::$profilerSteps = array();
        self::$profilerStart = microtime(true);
    }

    public static function addStep ($message) {
        self::$profilerSteps []= array(
            'time' => microtime(true),
            'message' => $message
        );
    }

    public static function logProfiler () {
        $name = self::$profilerName;
        $output = "
        *************************
        *************************
        PROFILE: $name
        *************************
        ";

        $prevTime = self::$profilerStart;
        foreach(self::$profilerSteps as $step) {
            $time = $step['time'] - $prevTime;
            $prevTime = $step['time'];
            $msg = $step['message'];
            $output.= "$time : $msg\n";
        }
        self::log($output);
    }

    private static function pushMsg ($msg, $obj = null) {
        $text = $msg;

        if($obj !== null)
        {
            $text = $msg . "\n" . var_export($obj, true);
        }

        $text .= "\n";

        if(self::$fileHandle !== null)
        {
            fwrite(self::$fileHandle, $text);
        }

        if(self::$sendToConsole) {
            echo $text;
        }

        self::$messages []= $text;
    }

    public static function __init () {
        if(RFConfig::isSetAndTrue('rfdevel')) {
            self::$fileHandle = fopen(sys_get_temp_dir() . "/rflog.txt", "a");
        }

        if(PHP_SAPI === "cli") {
            self::$sendToConsole = true;
        }
    }

    public static function __close () {
        self::log("Finished request at ".time());
        self::log("=================================\n");

        if(self::$fileHandle !== null)
        {
            fclose(self::$fileHandle);
        }
    }

    public static function __getMessages () {
        return self::$messages;
    }

    protected static $messages = array();

    protected static $fileHandle = null;
	protected static $sendToConsole = null;

}

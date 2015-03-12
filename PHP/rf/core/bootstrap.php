<?php
require_once "buildid.php";

// internal: Convert unix format to local (windows) path
function _fr_unixToLocalPath ($path)
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

require_once "scripts/requires.php";

session_start();
/**
 * Try autoloading a class
 *
 * @param $className
 * @return void
 */
function _fr_autoload ($className)
{
    // requires.php is generated from scripts/loadergen.php
    // $frClasses['Class_Name'] = 'path/to/file.php' w.r.t FR_FOLDER_ROOT
    global $frClasses;

    // check if the classes exists
    if(isset($frClasses[$className]))
    {
        /** @noinspection PhpIncludeInspection */
        require_once RF_FOLDER_ROOT.DIRECTORY_SEPARATOR."core/".$frClasses[$className];
    }
    // otherwise, try the vendor autoload
    else
    {
       // RFAssert::Exception("autoload for $className failed");
    }
}
spl_autoload_register('_fr_autoload');

if(!file_exists(RF_FOLDER_ROOT.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR."config.php"))
{
    die("Error! Looks like RazorFlow is not installed. Please open install.php in your browser.");
}

require_once RF_FOLDER_ROOT.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR."config.php";



function _rf_error_handler($errno, $errstr, $errfile, $errline)
{
    if(isset($GLOBALS['rfDisableErrors']))
        return;
    $trace = debug_backtrace();

    $backtrace = "";
    for($i = 0; $i < count($trace); $i++)
    {
        if(isset($trace[$i]['file']) && isset($trace[$i]['line']))
        {
            $backtrace .= $trace[$i]['file'].':'.$trace[$i]['line']."\n";
        }
    }

    RFUtil::Error($errstr, $backtrace);
}

set_error_handler('_rf_error_handler');


/*
 *
 * Initialize RazorFlow
 */
// Initialize Logging
RFLog::__init();
Dashboard::__init();

// now register a fatal error handler.
function rf_fatal_error_handler()
{
    if(isset($GLOBALS['rfDisableErrors']))
        return;
    $error = error_get_last();
    if($error)
    {
        RFLog::log("There was an error", $error);
        if(!headers_sent())
        {
            $message = $error['message'] .' at '.$error['file']. ':' . $error['line'];


            $trace = debug_backtrace();
            $backtrace = "";
            for($i = 0; $i < count($trace); $i++)
            {
                if(isset($trace[$i]['file']) && isset($trace[$i]['line']))
                {
                    $backtrace .= $trace[$i]['file'].':'.$trace[$i]['line']."\n";
                }
            }

            RFUtil::Error($message, $backtrace);
        }
    }
}
error_reporting(E_ALL);

// handle the fatal errors here.
register_shutdown_function('rf_fatal_error_handler');

// If default timezone is not set
if(ini_get('date.timezone') === '')
{
    // override the timezone
    ini_set('date.timezone', RFConfig::get('default_timezone'));
    date_default_timezone_set(RFConfig::get('default_timezone'));
}

// This is here for legacy reasons. TODO: Remove this.
define ("_RF_DTYPE_NUM", "number");
define ("_RF_DTYPE_TEXT", "string");
define ("_RF_DTYPE_TIME", "time");
define ("_RF_DTYPE_NULL", "null");


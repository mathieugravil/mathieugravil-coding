<?php


class RFUtil {
    protected static $url;
    public static function RequestRedirect($url) {
        self::$url = $url;
    }

    public static function emitJson ($object, $extraHeaders = array()) {
        if(headers_sent()) {
            die("Error! Please do not send any data directly to the browser. For logging, use Dashboard::log instead");
        }

        if(PHP_SAPI !== "cli")
        {
            // Set the content-type header.
            foreach($extraHeaders as $header)
            {
                header($header);
            }

            // VERY AGGRESSIVELY PREVENT CACHING!!!
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache"); 

            // Ensure it's parsed as JSON
            header('Content-Type: application/json;');
        }

        if(RFConfig::isSetAndTrue('debug'))
        {
            $object['logs'] = RFLog::__getMessages ();
            $object['rfdt'] = array(
                    'queries' => RFDevTools::getQueries (),
                    'dataSources' => RFDevTools::getDataSources(),
                    'diagnostics' => RFDevTools::getDiagnostics()
                );
        }

        if(isset(self::$url)) 
        {
            $object['redirect'] = self::$url;
        }


        $GLOBALS['rfDisableErrors'] = true;
        echo json_encode($object);
        exit();
    }


    /**
     * Throw an exception into JSON.
     *
     * @param Exception $ex
     */
    public static function Exception (Exception $ex) {
        RFLog::log("[ERROR][ERROR][ERROR][ERROR][ERROR][ERROR][ERROR][ERROR]");
        RFLog::log("Exception: ". $ex->getMessage());
        $trace = $ex->getTrace();
        if(isset($trace[0]['file']) && isset($trace[0]['line']))
            RFLog::log("Trace: ". $trace[0]['file'].":".$trace[0]['line']);
        RFLog::log("[ERROR][ERROR][ERROR][ERROR][ERROR][ERROR][ERROR][ERROR]");

        $traceMsg = "";
        foreach($ex->getTrace() as $item) 
        {
            if(isset($item['file']) && $item['line'])
                $traceMsg .= $item['file'].":".$item['line']."\n";
        }
        $object = array(
            'message' => $ex->getMessage(),
            'trace' => $traceMsg,
            'error' => 'RazorflowException'
        );

        $extraHeaders = array(
            'HTTP/1.1 500 Internal Exception;',
            'X-RazorFlow-Info: Exception'
        );

        self::emitJson($object, $extraHeaders);
    }

    public static function Error ($message, $trace = "") {
        RFLog::log("[ERROR][ERROR][ERROR][ERROR][ERROR][ERROR][ERROR][ERROR]");
        RFLog::log("Message: ", $message);
        RFLog::log("Trace: ", $trace);
        RFLog::log("[ERROR][ERROR][ERROR][ERROR][ERROR][ERROR][ERROR][ERROR]");
        $object = array(
            'message' => $message,
            'trace' => $trace,
            'error' => 'RazorflowError'
        );

        $extraHeaders = array(
            'HTTP/1.1 500 Internal Exception;',
            'X-RazorFlow-Info: Error'
        );

        if(!RFRequest::isHTMLRequest())
        {
            self::emitJson($object, $extraHeaders);
        }

    }

    public static function getSampleDataSource ()
    {
        if(!isset(self::$sampleDs))
        {
            self::$sampleDs = new SQLiteDataSource(RF_FOLDER_ROOT."/demos/databases/chinook.sqlite");

            self::$sampleDs->setSQLSource("InvoiceLine JOIN Invoice ON Invoice.InvoiceId = InvoiceLine.InvoiceId JOIN Track ON Track.TrackId = InvoiceLine.TrackId JOIN Album ON Track.AlbumId = Album.AlbumId JOIN Artist ON Album.ArtistId = Artist.ArtistId JOIN Genre ON Track.GenreId = Genre.GenreId");
        }

        return self::$sampleDs;
    }

    public static function getTempDir () {
        // Get the temporary directory.
        return sys_get_temp_dir();
    }

    public static function isCLI() {
        return PHP_SAPI === "cli";
    }

    protected static $sampleDs;
}


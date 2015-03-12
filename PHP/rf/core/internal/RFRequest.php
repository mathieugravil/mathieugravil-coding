<?php
class RFRequest
{
    public static function check($key)
    {
        if(isset($_GET[$key]) || isset($_POST[$key])) {
            return true;
        }

        return false;
    }

    public static function assertExists ($key) {
        if(!self::check($key))
            RFAssert::Exception("Error! Expecting $key in params");
    }

    public static function getAssets () {
        $staticRoot = RFConfig::get('webroot')."/static";

        $invalidator = '?v='.RFConfig::get('buildId');

        if(RFConfig::isSetAndTrue('rfdevel')) {
            $invalidator = '?='.rand(0, 10000);
            $scripts1 = array(
                "/lib/minified/jquery.min.js",
                "/lib/minified/json.min.js",
                "/lib/minified/modernizr.min.js",
                "/lib/minified/underscore.min.js",
                "/lib/minified/eventemitter.min.js",
                "/lib/minified/color.min.js",
                "/lib/minified/date.min.js",
                "/lib/minified/jquery.masonry.min.js",
                "/lib/minified/kendo/kendo.core.js",
                "/lib/minified/kendo/kendo.data.js",
                "/lib/minified/kendo/kendo.popup.js",
                "/lib/minified/kendo/kendo.fx.js",
                "/lib/minified/kendo/kendo.menu.js",
                "/lib/minified/kendo/kendo.userevents.js",
                "/lib/minified/kendo/kendo.draganddrop.js",
                "/lib/minified/kendo/kendo.window.js",
                "/lib/minified/kendo/kendo.upload.js",
                "/js/bootstrap.js",
                "/js/razorflow.js",
                "/js/component.js",
                "/js/tablecomponent.js",
                "/js/chartcomponent.js",
                "/js/kpicomponent.js",
                "/js/filtercomponent.js",
                "/js/colorrange.js",
                "/js/devtools.js",
                "/js/rfinfo.js",
                "/js/spreadsheethelper.js",
                "/js/rfauth.js"
            );
            
            if(isset($_GET['jqplugin'])) {
                // remove the first item from the index
                array_shift($scripts1);
            }

            $scripts2 = array(
                "/lib/minified/kendo/kendo.pager.js",
                "/lib/minified/kendo/kendo.binder.js",
                "/lib/minified/kendo/kendo.filtermenu.js",
                "/lib/minified/kendo/kendo.list.js",
                "/lib/minified/kendo/kendo.grid.js",
                "/lib/minified/kendo/kendo.selectable.js",
                "/lib/minified/kendo/kendo.calendar.js",
                "/lib/minified/kendo/kendo.datepicker.js",
                "/lib/minified/kendo/kendo.dropdownlist.js",
                "/lib/minified/kendo/kendo.list.js",
                "/lib/minified/kendo/kendo.listview.js",
                "/lib/minified/kendo/kendo.mobile.scroller.js",
                "/lib/minified/kendo/kendo.numerictextbox.js",
                "/lib/minified/kendo/kendo.pager.js",
                "/lib/minified/kendo/kendo.splitter.js",
                "/lib/minified/kendo/kendo.sortable.js",
                "/lib/minified/kendo/kendo.reorderable.js",
                "/lib/minified/kendo/kendo.resizable.js",
                "/lib/minified/kendo/kendo.treeview.js",
                "/js/rffiltermenu.js",
                "/lib/minified/RFChart.js" 
                // "/js/fc/FusionCharts.js",
                // "/js/fc/FusionCharts.HC.js",
                // "/js/fc/FusionCharts.HC.Charts.js",
            );
            $theme = 'silver';
            if(isset($_GET['theme']))
            {
                $theme = preg_replace("[^A-Za-z0-9]", "", $_GET['theme']);
            }
            $stylesheets = array("razorflow.$theme.css");

        }
        else {
            $scripts1 = array(
                '/build/stage0.js'
            );
            if(isset($_GET['jqplugin'])) {
                $scripts1 = array('/build/stage0.nojquery.js');
            }
            $scripts2 = array(
                '/build/stage1.js'
            );
            $theme = RFConfig::get("theme");
            $stylesheets = array("razorflow.$theme.css");
        }



        foreach ($stylesheets as &$style) {
            $style = "$staticRoot/css/$style";
        }

        foreach ($scripts1 as &$script) {
            $script = $staticRoot.$script.$invalidator;
        }

        foreach ($scripts2 as &$script) {
            $script = $staticRoot.$script.$invalidator;
        }

        return array($scripts1, $scripts2, $stylesheets);
    }

    public static function get($key, $default = null) {
        $value = null;
        if(isset($_GET[$key]))
            $value = $_GET[$key];

        if(isset($_POST[$key]))
        {
            $value = $_POST[$key];
        }

        if($value !== null) {
            if(function_exists('get_magic_quotes_gpc') && $value !== null) {
                if(get_magic_quotes_gpc())
                {
                    if(is_string($value))
                    {
                        $value = stripslashes($value);
                    }
                }
                return $value;
            }
            else {
                return $value;
            }
        }

        return $default;
    }

    public static function isHtmlRequest () {
        $webFlag = (!(self::check('dbAsJson') || self::check('endpoint') || self::check('componentRPC')));

        return $webFlag && !(PHP_SAPI==="cli");
    }

    protected static $postBack;

    public static function getPostback () {
        if(isset(self::$postBack))
            return self::$postBack;

        $postBackContent = RFRequest::get('postback', '{}');

        self::$postBack = json_decode($postBackContent, true);

        return self::$postBack;
    }

    public static function getBuildableUrl () {
        $gettableUrl = "";
        if(isset($_SERVER["REQUEST_URI"]))
        {
            $url = $_SERVER["REQUEST_URI"];
            $uriComponents = parse_url($url);
            if(!isset($uriComponents["query"]) && substr($url, -1) !== "?")
                $gettableUrl = $_SERVER["REQUEST_URI"]."?x";
            else
                $gettableUrl = $_SERVER["REQUEST_URI"];
        }

        return $gettableUrl;
    }

    public static function getDashboardUrl ($params = array(), $unset = array()) {
        $url = "";
        if(isset($_SERVER["REQUEST_URI"]))
        {
            $url = $_SERVER["REQUEST_URI"];
            $uriComponents = parse_url($url);
            
            if(isset($uriComponents['query']))
            {
                $queryAsArray = parse_str($uriComponents['query']);
            }
            else {
                $queryAsArray = array();
            }
            

            foreach($params as $key => $value) {
                $queryAsArray[$key] = $value;
            }

            foreach($unset as $item) {
                if(isset($queryAsArray[$item]))
                {
                    unset($queryAsArray[$item]);
                }
            }

            if(!is_array($queryAsArray)){
                $queryAsArray = array();
            }

            if(count($queryAsArray) > 0)
            {
                $uriComponents['query'] = http_build_query($queryAsArray);
            }
            else {
                unset($uriComponents['query']);
            }

            $url = self::unparse_url($uriComponents);
        }

        return $url;
    }

    public static function getEndpoint () {
        return self::get('endpoint', '');
    }
    public static function unparse_url($parsed_url) { 
      $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
      $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
      $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
      $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
      $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
      $pass     = ($user || $pass) ? "$pass@" : ''; 
      $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
      $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
      $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
      return "$scheme$user$pass$host$port$path$query$fragment"; 
    } 

}

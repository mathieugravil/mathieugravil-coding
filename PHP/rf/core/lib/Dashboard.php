<?php
class DashboardOptions extends RFOptions {
    /**
     * For internal use
     * @internal
     * 
     * @var string
     */
    public $__title;

    /**
     * For internal use
     * @internal
     * 
     * @var string
     */
    public $__header;

    /**
     * For internal use
     * @internal
     * 
     * @var string
     */
    public $__footer;

    public $__webRoot;

    public $__assetList;

    public $__loggedInAs;

    public $__logoutUrl;

    /**
     * The width of the dashboard. It can be set to an absolute
     * pixel width or left to "auto" to automatically adjust the width
     * 
     * @var string
     */
    public $width = 'auto';

    public function __inheritedClassName() {
        return __CLASS__;
    }
}

/**
 * The main dashboard class. All methods in this function are
 * static methods. So you need to call it like ::
 *
 *     Dashboard::addComponent($component);
 *
 * You need to call ``Dashboard::Render();`` after your dashboard
 * script to render the dashboard.
 */
class Dashboard
{
    /**
     * Add a component to the dashboard.
     * 
     * @param Component $component The component to add to the dashboard
     */
    public static function addComponent ($component) {
        self::$components[$component->ID] = $component;

    }

    /**
     * Set the title of the dashboard.
     * 
     * @param string $title The Title of the Dashboard
     */
    public static function setTitle($title) {
        self::$options->__title = $title;
    }

    /**
     * Set the footer text of the dashboard.
     * 
     * @param string $footer The footer text of the dashboard.
     */
    public static function setFooterText($footer) {
        self::$options->__footer = $footer;
    }

    public static function __setOption($name, $value) {
        self::$options->$name = $value;
    }

    /**
     * Set the default values for a particular options class
     * 
     * @param string $optionClassName Name of the class to set the options for
     * @param array $options Key-value array of default values
     */
    public static function setDefaults ($optionClassName, $options)
    {
        if(!isset(self::$__optDefaults[$optionClassName]))
        {
            self::$__optDefaults[$optionClassName] = array();
        }
        
        self::$__optDefaults[$optionClassName] = array_merge(
            self::$__optDefaults[$optionClassName],
            $options
        );
    }


    /**
     * Set the width of the dashboard.
     *
     * By default, the width of the dashboard is automatically determined
     * but you can set it to a specific pixel width for the dashboard.
     *
     * **Note** - this will only affect the dashboard in desktop mode, and 
     * not in mobile mode
     * 
     * @param int $width The width of the dashboard.
     */
    public static function setWidth($width) {
        self::$options->width = $width;
    }

    private static function HandleCLI () {
        RFLog::log("Handling cli");
        /** @var $component RFComponent */
        foreach(self::$components as $component) {
            $component->__handleRefresh();
        }

        /** @var $ds DataSource */
        foreach(self::$datasources as $ds) {
            $ds->__handleRefresh();
        }
    }

    public static function __registerDataSource ($ds) {
        self::$datasources []= $ds;
    }

    /**
     * Main execution function for RazorFlow PHP.
     *
     * This method displays the dashboard on the browser.
     */
    public static function Render () {
        if(RFUtil::isCLI()) {
            self::HandleCLI();
            return;
        }


        if(SocialAuth::_usingSocialAuth()) {
            SocialAuth::_OnDashboardInit();
        }

        if(RFRequest::isHTMLRequest())
        {
            require RF_FOLDER_ROOT."/core/scripts/renderdashboard.php";
            exit();
        }



        // guard against calling Render() Twice
        if(self::$renderFlag)
        {
            return 0;
        }
        self::$renderFlag = true;

        // Encase the entire application in a try block, to catch
        // all exceptions in a top level
        try
        {
            if(RFRequest::check('endpoint'))
            {
                $action = RFRequest::get('endpoint');

                if($action !== 'triggerAction')
                    RFMessageBroker::routeMessageUnsafe($action);
            }

            // Before doing any of this processing, we are going to emit the
            // 'dashboardBeforeProcess' signal
            RFMessageBroker::trigger('dashboardBeforeProcess');

            // See if the request is asking for an 'endpoint'
            // the endpoint is a relic of a slightly older system where
            // there were multiple endpoints like updateDataset, getDataAsCSV, etc.
            //
            // While it's not required, it might be possible there might be something
            // better handled by endpoints, so we are keeping this around.
            if(RFRequest::check('endpoint'))
            {
                $action = RFRequest::get('endpoint');

                if($action === 'triggerAction')
                {
                    // Trigger the action.
                    $response = self::triggerAction ();

                    RFUtil::emitJSON($response);
                }
                else {
                    RFMessageBroker::routeMessage($action);
                }
            }
            // dbAsJson is the parameter passed to get the dashboard as a JSON script
            // this is the first thing called after the dashboard is loaded.
            else if(RFRequest::check('dbAsJson') || isset($GLOBALS['dbAsJson']))
            {
                $response = self::RenderProp();

                RFUtil::emitJSON($response);
            }

            // Component RPC. This allows the component to expose particular methods (like updating dataset)
            // etc, and the client components can call these methods by using a wrapper function
            // exposed in javascript
            else if(RFRequest::check('componentRPC')){
                // Get tjhe RPC Name and parameters.
                $rpcName = RFRequest::get('rpcName');
                $rpcID = RFRequest::get('rpcID');

                $params = RFRequest::get('params');
                $params = json_decode($params);

                // see if the component exists
                if(isset(self::$components[$rpcID]))
                {
                    self::applyAllActionsInPostback($rpcID);

                    /** @var $component Component */
                    $component = self::$components[$rpcID];

                    $response = $component->__handleRPC($rpcName, $params);

                    // If rpcAsLink is true, then let the component output whatever it wants.
                    // for example, in the "export to CSV" functionality we need to send data
                    // back to client in a way that can't be done with JSON.

                    // So we set rpcAsLink to true and let the component RPC function set the
                    // response headersand emit a CSV.

                    if(!RFRequest::check('rpcAsLink'))
                    {
                        RFUtil::emitJSON($response);
                    }
                }
            }
        }
        catch(Exception $ex)
        {

            // If there's a non-fatal error (something thrown by the framework itself), then just
            // fail after printing the exception. The exception is also in JSON so the error will be
            // displayed very cleanly to the user.
            RFUtil::Exception($ex);
        }

        RFLog::__close();
        return 0;
    }

    /**
     * Applies all action in postback.
     *
     * The postback contains the state of the dashboard. This may include previously
     * clicked components, etc.
     *
     * While there's no need to process the actual _data_ of the affected components
     * it is mandatory to apply all the actions.
     *
     * @param $sourceId
     */
    private static function applyAllActionsInPostback($sourceId)
    {
        $postback = RFRequest::getPostBack();


        if(!isset($postback['actions']))
            return;


        // Execute the source's postback first so there might be some changes
        // source might make to the overall train, even those influencing other actions
        if(isset($postback['actions'][$sourceId]))
        {
            $sourceObj = $postback['actions'][$sourceId];

            /** @var $sourceComp Component */
            $sourceComp = self::$components[$sourceId];
            $sourceComp->__triggerAction($sourceObj['actionName'], $sourceObj['params']);

            // don't process the source twice.
            unset($postback['actions'][$sourceId]);
        }

        // Get the remaining actions in the postback.
        foreach($postback['actions'] as $id => $action)
        {
            /** @var $source Component */
            $source = self::$components[$id];
            $actionName = $action['actionName'];
            $params = $action['params'];

            $source->__triggerAction($actionName, $params);
        }
    }

    /**
     * Trigger an action from the postback
     */
    private static function triggerAction()
    {
        $sourceId = RFRequest::get("source");

        RFAssert::HasKey("Component must exist", $sourceId, self::$components);

        // Get the source component of the action
        /** @var $source Component */
        $source = self::$components[$sourceId];

        // First, evaluate the postback so all the previous clicks are replicated
        self::applyAllActionsInPostback($sourceId);

        // Tell the component that the action has been triggered.
        $modifiedList = $source->__getAffectedTargets();

        RFLog::log("The count of the modified list is ", count($modifiedList));

        // Create array of components and datasets that will be changed
        $components = array();
        $datasets = array();

        /** @var $component Component */
        foreach($modifiedList as $component)
        {
            // Populate components and datasets
            $components[$component->getID()] = $component->__getProperties();
            $datasets[$component->getID()] = $component->__getDataSet();
        }

        // Return array which will be sent back to the dashboard. All the components will be redrawn
        return array(
            'components' => $components,
            'datasets' => $datasets
        );
    }


    /**
     * Main entry point of the application, which kicks off component actions or a render
     *
     * This returns the object, and not the actual JSON, which helps with unit testing.
     *
     * @return null
     */
    protected static function RenderProp()
    {
        $properties = array();
        $properties['components'] = array();
        $properties['datasets'] = array();

        // set other options
        self::$options->__webRoot = RFConfig::get('webroot');

        self::$options->__assetList = RFRequest::getAssets();

        $properties['dashboard'] = self::$options->asArray();

        /** @var $component Component */
        foreach(self::$components as $component)
        {
            // Get the properties of each
            $properties['components'][$component->ID] = $component->__getProperties();
            $properties['datasets'][$component->ID] = $component->__getDataSet();
        }

        RFMessageBroker::trigger('beforeDashboardEmit', array());

        // Attach the message broker messages only for the first load...
        $properties['messages'] = RFMessageBroker::getMessages();

        // Render the properties to be serialized by JSON
        return $properties;
    }

    public static function __init() {
        self::$options = new DashboardOptions();
    }


    protected static $renderFlag = false;

    protected static $components = array();

    protected static $datasources = array();

    /**
     * @var DashboardOptions
     */
    protected static $options;

    public static $__optDefaults = array();
}


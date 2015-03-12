<?php
/**
 * The core class for all components
 */
abstract class RFComponent extends RFObject{

    /**
     * @param string $message The message to log
     */
    public function log($message, $obj = null)
    {
        $id = $this->ID;
        RFLog::log("[$id] $message", $obj);
    }

    /**
     * @param string $message The message to log
     */
    protected function debug ($message, $obj = null)
    {
        $id = $this->ID;
        RFLog::debug("[$id] $message", $obj);
    }



    /**
     * Sets one or more options for this component.
     * 
     * You can call setOption in the following ways:
     * 
     * 1. With two params:
     * 
     * $component->setOption('caption', "Hello!");
     * 
     * 2. As an array of key-value pairs ::
     * 
     *     $component->setOption (array(
     *          'caption' => "Hello",
     *          'numberPrefix' => '$'
     *     ));
     * 
     * 3. As an object of the Options class for the component you're using.
     * for example, if you're using it to configure a chart component, you can say ::
     * 
     *     $opt = new ChartOptions();
     *     $opt->caption = "hello";
     *     $opt->numberPrefix = "$";
     *     $component->setOption($opt);
     *
     * @return void
     */
    public function setOption()
    {
        $optArray = array();

        if(func_num_args() === 2)
        {
            $key = func_get_arg(0);
            $val = func_get_arg(1);
            RFAssert::Assert("The key must be a string", is_string($key));

            $optArray = array(
                $key => $val
            );
        }
        else if(func_num_args() === 1)
        {
            $optArray = func_get_arg(0);

            if(!is_array($optArray))
            {
                // see if there is an 'asArray' method
                if(method_exists($optArray, 'asArray'))
                    $optArray = $optArray->asArray();
                else
                    RFAssert::Exception("setOption only accepts arrays or ".$this->getOptionClassName()." objects");

            }
        }
        else{
            RFAssert::Exception("Please call setOption with an option Key and Value");
        }

        $this->optionContainer = array_merge($this->optionContainer, $optArray);
    }

    /**
     * Set the caption for this component.
     *
     * @param string $caption
     */
    public function setCaption ($caption, $placeHolderCaption = "")
    {
        $this->caption = $caption;
        if(strlen($placeHolderCaption) > 0)
        {
            $this->placeholderCaption = $caption;
            $this->caption = $placeHolderCaption;
        }
    }


    /**
     *
     * @param $caption string caption
     */
    public function setPlaceholder ($caption) {
        $this->placeholderText = $caption;
    }

    /**
     * Set the width class for this component.
     *
     * The width can be 1 to 4.
     *
     * See :ref:`dimensions` for more information on this
     * 
     * @param int $width The width in Units of 1 to 4
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Set the height class for this component.
     *
     * See :ref:`dimensions` for more information on this
     * 
     * @param int $width The height in Units
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Set the dimensions for this component
     *
     * The width can be between 1 to 4.
     *
     * See :ref:`dimensions` for more information.
     * 
     * @param int $width  The width
     * @param int $height The height
     */
    public function setDimensions ($width, $height)
    {
       $this->isValidDimension($width);
       $this->isValidDimension($height);
       $this->setWidth ($width);
       $this->setHeight ($height);
    }

    public function __handleRefresh () {

    }

    protected function isValidDimension ($amount) {
        RFAssert::Assert("The dimension needs to be an integer between 1 and 4",(is_int($amount) && $amount <= 4 && $amount >= 1));
    }

    /**
     * The caption for the component
     * @var string
     */
    protected $caption = "";


    /**
     * The string to be used as a placeholder if this component requires an action first.
     * @var string
     */
    protected $drillPlaceholder;

    /**
     * The width of the component. Note: This is not measured in pixels.
     * 
     * See :ref:`dimensions` for more information.
     * @var integer
     */
    protected $width = 2;

    /**
     * The height of the component. Note: This is not measured in pixels.
     * 
     * Please see :ref:`dimensions` for more information.
     * @var integer
     */
    protected $height = 2;

    /**
     * The placeholder caption when no drill downs are applied
     * @var string
     */
    protected $placeholderCaption;


    protected $placeholderText;

    /**
     * A flag whether a component has actions
     * 
     * @var boolean
     */
    protected $hasActionsFlag = false;

    /**
     * An array that contains all the log messages that will be sent to the client
     * 
     * @var array
     */
    protected $logMessages = array();

    /**
     * An array to hold all of the options that the users have specified.
     * 
     * A component has to manually handle the options.
     * 
     * @var array
     */
    protected $optionContainer = array();

    /**
     * All the options for the component.
     * 
     * @var RFOptions
     */
    protected $options;

    /**
     * The core properties of the Component. This is going to be serialized and 
     * sent to the client
     * 
     * @var array
     */
    protected $properties = array();

    /**
     * All the actions of the component.
     * 
     * @var array
     */
    protected $actions = array();

    /**
     * Boolean flag. Is this component chromeless?
     * 
     * @var boolean
     */
    protected $chromeless = false;

    /**
     * Flag. Has the component been initialized? yes/no
     * 
     * @var boolean
     */
    protected $_initialized = false;

    /**
     * Intialize method, which is called to populate the properties array
     * 
     * @return boolean
     */
    protected function initialize (){
        // Call loadOptions to pick up all the options that were passed to 
        // setOptions
        
        $this->loadOptions();
        return false;
    }

    /**
     * A wrapper around initialize() to prevent it from running twice
     * in case it is called twice because of actions
     * 
     * @return void
     */
    protected function _initializeWrapper ()
    {
        if(!$this->_initialized)
        {
            $this->_initialized = true;
            $this->initialize();
            $this->postInitialize();
        }
    }


    public function __getChildComponents() {
        return array();
    }

    protected function postInitialize ()
    {
        $this->loadOptions();
        return false;
    }

    protected $actionList = array();

    protected $resultList = array();

    /**
     * A function that is used to load the options from optionContainer
     * into the properties.
     * 
     * Instead of blindly loading all properties, we will require individual components
     * to override this function and load the properties
     * 
     * @return null
     */
    protected function loadOptions ()
    {
        // As the component to provide the class name for the options
        $optClassName = $this->getOptionClassName();

        // If no option class name provided, simply return empty properties
        if($optClassName === "")
        {
            $this->properties['opt'] = array();
            return;
        }

        /** @var $optClass RFOptions */
        $optClass = new $optClassName($this->optionContainer);
        $this->options = $optClass;

        // Convert the options into an array and 
        $this->properties['opt'] = $optArray = $optClass->asArray();
    }


    /**
     * A method that let's a component developer specify the options class
     * name
     *
     * @return string
     */
    protected function getOptionClassName ()
    {
        return "";
    }

    /**
     * Internal function for registering an action
     * @param string $name
     * @param function $callback
     * @param RFComponent $target
     * @param EventOptions|array $options
     */
    protected function registerAction ($name, $callback, $target, $options = array())
    {
        RFAssert::IsA("The target for an action should be a component", $target, 'RFComponent');

        RFAssert::Assert("The callback needs to be valid", is_callable($callback));

        $this->simpleRegisterAction($target, $options);

        $opt = new EventOptions($options);

        $this->hasActionsFlag = true;

        // add this action to the action list
        $this->actionList []= array(
            'type' => $name,
            'callback' => $callback,
            'target' => $target,
            'options' => $opt
        );
    }

    /**
     * A simpler form of registerAction in case the action isn't an external callback
     * (for example, a local drill)
     *
     * @param RFComponent $target
     * @param array $options
     */
    protected function simpleRegisterAction($target, $options = array())
    {
        $opt = new EventOptions($options);
        $this->resultList []= array(
            'target' => $target->getID(),
            'opt' => $opt->asArray()
        );
    }

    protected $affectedTargets = array();

    /**
     * Entry point for the core dashboard management routine
     * to trigger actions inside the component
     *
     * @param string $actionName
     * @param array $params
     * @return array
     */
    public function __triggerAction($actionName, $params)
    {
        // Trigger action returns a list of all the components
        $affectedTargets = array();

        $id = $this->getID();
        RFLog::log("Triggering action on $id with params:  ", $params);

        foreach($this->actionList as $item)
        {
            if($item['type'] === $actionName)
            {
                $item['callback']($params, $item['target']);

                $affectedTargets []= $item['target'];
            }
        }

        // Also call $this->onActionTriggered() and merge the results with this
        // onActionTriggered will handle drilldowns, etc.
        $affectedTargets = array_merge($affectedTargets, $this->onActionTriggered($actionName, $params));

        $this->affectedTargets = $affectedTargets;

        /** @var $target RFComponent */
        foreach($affectedTargets as $target)
        {
            // Take each target and pass the drill params so that the
            // target component can replace all the search terns with value
            $target->__registerDrilledParams($params);
        }

        return $affectedTargets;
    }

    /**
     * An external facing function, which allows the captions to be dynamic.
     *
     * For example, if the user says "Sales for {{label}}"
     * And the parameters for the original action source is:
     * $params = array('label' => "Spain");
     *
     * The new caption for this component will be:
     * "Sales for Spain"
     *
     * @param array $params
     */
    public function __registerDrilledParams($params)
    {
        if(isset($this->placeholderCaption))
        {
            foreach($params as $key => $value)
            {
                if(is_array($value))
                    continue;
                $searchParam = "{{".strtolower($key)."}}";

                $replaceParam = (string)$value;

                // Do a search and replace
                $this->caption = str_ireplace($searchParam, $replaceParam, $this->caption);
            }
            // make sure the placeholder caption is removed
            unset($this->placeholderCaption);
        }
    }

    /**
     * Gets a list of all the components that will be modified in case this component
     * has an action triggered.
     * 
     * @ignore
     * 
     * @return array
     */
    public function __getAffectedTargets()
    {
        return $this->affectedTargets;
    }


    /**
     * This function is meant to be overridden
     * 
     * All the compoents returned as an array will be refreshed in the dashboard
     * 
     * @ignore
     * 
     * @param  string $actionName The name of the action that is being triggered
     * @param  array $params     The parameters of the action
     * @return array
     */
    protected function onActionTriggered($actionName, $params) {
        return array();
    }

    /**
     * Handling an RPC From the client triggered from the JavaScript
     * @ignore
     * 
     * @param  string $rpcName  The RPC name
     * @param  array $params   The parameters of the RPC sent from client side
     * @param  array  $postBack The postback (current state of the dashboard)
     * @return boolean
     */
    public function __handleRPC($rpcName, $params, $postBack = array())
    {
        $this->debug("Hadling RPC $rpcName with postback", $postBack);
        return false;
    }


    /**
     * The function that is called to retrieve all the properties from RFWebReport.
     * 
     * This is an internal function. please do not use for dashboards.
     * @ignore
     * 
     * @return array
     */
    public function __getProperties ()
    {
        $this->properties['width'] = $this->width;
        $this->properties['height'] = $this->height;

        if(isset($this->placeholderCaption))
        {
            $this->properties['caption'] = $this->placeholderCaption;
        }
        else
        {
            $this->properties['caption'] = $this->caption;

        }
        $this->properties['id'] = $this->getID();
        $this->properties['type'] = $this->getObjectType();
        $this->properties['chromeless'] = $this->chromeless;
        $this->properties['resultList'] = $this->resultList;

        if(isset($this->placeholderText))
        {
            $this->properties['placeholderText'] = $this->placeholderText;
        }

        $this->_initializeWrapper();

        $this->properties['hasActionsFlag'] = $this->hasActionsFlag;

        return $this->properties;
    }
}

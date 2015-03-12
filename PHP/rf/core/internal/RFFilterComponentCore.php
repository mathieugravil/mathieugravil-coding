<?php

class RFFilterComponentCore extends Component
{
    protected function addTextItem($key, $caption, $default = "")
    {
        $this->items[$key] = array(
            'type' => 'text',
            'caption' => $caption,
            'defaultValue' => $default
        );
    }

    protected function addBooleanItem ($key, $caption, $default = false)
    {
        $this->items[$key] = array(
            'type' => 'bool',
            'caption' => $caption,
            'defaultValue' => $default
        );
    }

    protected function addSelectItem ($key, $caption, $values, $default = array())
    {
        $this->items[$key] = array(
            'type' => 'select',
            'caption' => $caption,
            'values' => $values,
            'defaultValue' => $default
        );
    }

    protected function addMultiSelectItem($key, $caption, $values, $default = array())
    {
        $this->items[$key] = array(
            'type' => 'multiselect',
            'caption' => $caption,
            'values' => $values,
            'defaultValue' => $default
        );
    }

    protected function addTimeRangeItem ($key, $caption, $default = array())
    {
        $this->items[$key] = array(
            'type' => 'timerange',
            'caption' => $caption,
            'defaultValue' => $default
        );
    }

    protected function addNumericRangeItem ($key, $caption, $default = array())
    {
        $this->items[$key] = array(
            'type' => 'numrange',
            'caption' => $caption,
            'defaultValue' => $default
        );
    }

    protected $items = array();

    protected function initialize()
    {
        $init = parent::initialize();
        RFLog::log("initializing chart component");
        if ($init)
            return $init;

        $this->addItemsToProperties();

        return false;
    }

    /**
     * This function is for internal use. In components like AutoFilter
     * we add items in the initialize() function. But since the items array
     * has already been populated, the changes won't be reflected in the client
     */
    protected function addItemsToProperties (){
        $this->properties['items'] = $this->items;

    }

    public function onFilterApplied($callback, $target, $eventOptions = array())
    {
        $this->registerAction('filterApplied', $callback, $target, $eventOptions);
    }

    /**
     * @return string
     */
    public function getObjectType()
    {
        return __CLASS__;
    }
}

/**
 * Currently there are no customizations to the filter item options
 * 
 */
class FilterItemOptions extends RFOptions {
    public function __inheritedClassName () {
        return __CLASS__;
    }
}
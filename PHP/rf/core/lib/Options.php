<?php

class NumberFormatOptions extends RFOptions {
    /**
     * Using this attribute, you could add prefix to all the numbers.
     * For example, to represent all dollars figures, you could specify 
     * this attribute to '$' to show like $40000, $50000. 
     * 
     * @var string
     */
    public $numberPrefix;

    /**
     * Using this attribute, you could add a suffix to all the numbers.
     * For example, to represent all dollars figures, you could specify 
     * this attribute to '%' to show like 33%, 53%. 
     * 
     * @var string
     */
    public $numberSuffix;

    /**
     * If this is set to false, the number will not be formatted. It will
     * be shown without commas or decimals.
     * 
     * @var boolean
     */
    public $formatNumber = true;

    /**
     * Number of decimal places all items in the chart will be rounded to
     * 
     * @var int
     */
    public $decimals;

    public function __inheritedClassName ()
    {
        return __CLASS__;
    }
}

class ColumnOptions extends NumberFormatOptions
{
    /**
     * The name of the column
     * @var string
     */
    public $name = "";

    /**
     * The time format string
     *
     * Options:
     * "d" - short date - "11/6/2010"
     *
     * TODO: Add the remaining options
     * @var string
     */
    public $timeFormatString = "d";

    /**
     * function for internal use only.
     * 
     * @internal
     */
    public $__ranged;

    /**
     * function for internal use only.
     * 
     * @internal
     */
    public $__fillMode = 0;

    /**
     * The type of data
     * 
     * @var string
     */
    public $type = "auto";

    public function __inheritedClassName ()
    {
        return __CLASS__;
    }
}

class ComponentOptions extends RFOptions{
    public function __inheritedClassName ()
    {
        return __CLASS__;
    }
}


class TableColumnOptions extends ColumnOptions
{
    /**
     * how to group by in an aggergate
     * @var bool
     */
    public $aggregate = false;

    /**
     * Should we group by this column?
     * @var bool
     */
    public $groupBy = false;

    /**
     * the aggregate type
     * @var string
     */
    public $aggregateFunction = 'SUM';

    /**
     * is this the primary column? y/n
     * @var bool
     */
    public $primaryColumn = false;

    /**
     * Whether to display this column as a ranged value
     * @var string
     */
    public $displayAsRange;

    /**
     * If the value is being displayed as a time-based range,
     * What kind of ranging should be used
     *
     * @var string
     */
    public $timeUnit = "yearly";

    /**
     * Sort the column. Possible values - ASC and DESC
     * @var string
     */
    public $sort = "";

    /**
     * A string that is prefixed before every number
     * @var string
     */
    public $numberPrefix = "";

    /**
     * The color of the text. This can either be a web-safe color (red, green, etc)
     * or can be a hexadecimal string.
     * 
     * @var string
     */
    public $textColor;

    /**
     * Show the text as bold.
     *
     * The text in the column will be bold when this is set to true
     * 
     * @var boolean
     */
    public $boldText = false;

    /**
     * Show the text as italics
     *
     * The text in the column will be italics when this is set to true
     * 
     * @var boolean
     */
    public $italicText = false;

    /**
     * Set the text alignment for the column
     *
     * Valid options are:
     * * left
     * * center
     * * right
     * 
     * @var boolean
     */
    public $textAlign;

    /**
     * Width of the column in Pixels
     * 
     * @var int
     */
    public $width = 130;

    /**
     * @internal
     * If this is set to true, the data is fetched but not sent back to client
     *
     * This is used for fetchColumn and addCustomColumn
     *
     * @var bool
     */
    public $__hidden = false;

    /**
     * Force the system to override the IDs while creating the processed data
     * @var string
     */
    public $__overrideID;

    /**
     * if set to true, the table will display the string as html
     *
     * @var bool
     */
    public $__rawHTML;

    public function __inheritedClassName()
    {
        return __CLASS__;
    }

    public function getIgnoreList ()
    {
        return array('aggregate', 'groupBy', 'aggregateFunction', 'sort');
    }
}



class EventOptions extends RFOptions {
    public function __inheritedClassName(){
        return __CLASS__;
    }
}
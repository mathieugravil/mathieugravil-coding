<?php



abstract class RFKpiComponentCore extends Component{

    /**
     * The timestamp expression is a SQL Expression which usually
     * corresponds to a DATETIME column in your DataSource.
     * 
     * @param DBExpression $timestampExp The timestamp expression
     * @param KPITimestampOptions $kpiTimestampOptions
     */
    public function setTimestampExpression($timestampExp, $kpiTimestampOptions = array())
    {
        $this->invalidate(func_get_args());
        $this->assertDataSourceLinked();
        $this->tsExp = DBExpression::__consume($timestampExp, $this);
        $this->tsOpt = new KPITimestampOptions($kpiTimestampOptions);
    }

    /**
     * The Value Expression is a SQL Expression which is used to determine
     * the value of the KPI that will be displayed to your users.
     * 
     * @param DBExpression $valueExp The value expression
     * @param KPIValueOptions $kpiValueOptions
     */
    public function setValueExpression($valueExp, $kpiValueOptions = array())
    {
        $this->invalidate(func_get_args());
        $this->assertDataSourceLinked();
        $this->valExp = DBExpression::__consume($valueExp, $this);
        $this->valOpt = new KPIValueOptions($kpiValueOptions);
    }

    /**
     * Set the value from a SQL Query, for when the value of the KPI cannot
     * be determined from timestamp and value expressions.
     *
     * Please note the following:
     * 
     * 1. The Query will be executed as provided, and will not be modified. This might have some security considerations
     * 2. KPIs with values from SQL Query cannot be filtered, or have a drill down from another component.
     * 3. If you do not add a "LIMIT" Clause to the Query, and there are too many rows, PHP might run out of memory
     * 4. The first column of the first row will be taken as the KPI Value. If you wish to override this, please set the :php:attr:`KPIValueFromQueryOptions::$valueColumn`
     * 
     * @param string $queryString The SQL Query to execute for the KPI
     * @param KPIValueFromQueryOptions  $options Options to customize how the query's data is used
     */
    public function setValueFromSQLQuery ($queryString, $options = array()) {
        $this->valueFromQuery = $queryString;

        $this->valueFromQueryOpt = new KPIValueFromQueryOptions($options);
    }

    /**
     * Count the number of distinct occurences of the value expression.
     * 
     * @param DBExpression $valueExp the value expression
     */
    public function countDistinct($valueExp)
    {
        $this->setValueExpression($valueExp, array('countDistinct' => true));
    }

    /**
     * Set a static current value for KPI.
     * 
     * @param number $value The value that will be displayed.
     * @param KPIValueOptions $options
     */
    public function setStaticCurrentValue ($value, $options = array())
    {
        $this->scalarMode = true;
        $this->currentValue = $value;
        $this->currentOpts = new KPIValueOptions($options);
    }
    
    /**
     * Set static past values of a KPI.
     *
     * These values will be displayed in the miniature spark chart
     * below the KPI Value that is displayed on the screen.
     * 
     * @param array $values an array of values to be displayed.
     */
    public function setStaticPastValues ($values)
    {
        $this->scalarMode = true;
        $this->pastValues = $values;
    }

    /**
     * Adds a target that will be marked on the gauge
     * 
     * @param number $value The value at which the target is marked
     * @param string $name The name of the target
     */
    public function addTarget($value, $name = "")
    {
        $this->targets []= array(
            'value' => $value,
            'name' => $name
        );

    }

    public function setSparkLineOptions($opt)
    {
        $this->sparkLineOpt = new SparkLineOptions($opt);
    }

    public function getOptionClassName()
    {
        return "KPIComponentOptions";
    }

    /**
     * Set the :php:class:`ColorRange` object.
     *
     * This object specifies the colors to be used while displaying
     * the gauge in Gauge KPIs
     * 
     * @param ColorRange $colorRanges The Color Range object
     */
    public function setColorRanges($colorRanges)
    {
        $this->setOption('colorRanges', $colorRanges);
    }

    /**
     * Set the "Key Points". The Key Points are used to 
     * divide the gauge and show a different color in each division.
     *
     * If you do not specify the colors, they will be automatically
     * generated according to the theme.
     *
     * @param array $keyPoints An array of numbers used to demarkate the gauge
     * @param array $colors An optional array of colors which 
     */
    public function setKeyPoints ($keyPoints, $colors = array()) {
        $ranges = new ColorRange();
        $ranges->setKeyPoints($keyPoints, $colors);
        $this->setColorRanges($ranges);
    }

    /**
     * Set the "Key Points". The Key Points are used to 
     * divide the gauge and show a different color in each division.
     *
     * However, if the Max/Min of the gauge vary, you can set key percentages
     * instead, which are automatically converted into key points 
     * based on the limits of the gauge.
     *
     * If you do not specify the colors, they will be automatically
     * generated according to the theme.
     *
     * @param array $keyPoints An array of numbers used to demarkate the gauge
     * @param array $colors Which colors to show for each section of the gauge
     */
    public function setKeyPercentages ($keyPoints, $colors = array()) {
        $ranges = new ColorRange();
        $ranges->setKeyPercentages($keyPoints, $colors);
        $this->setColorRanges($ranges);
    }

    /**
     * The timestamp expression
     * 
     * @var DBExpression
     */
    protected $tsExp;

    /**
     * Array to store the targets for gauges
     * 
     * @var array
     */
    protected $targets;

    /**
     * Timestamp options
     * 
     * @var KPITimestampOptions
     */
    protected $tsOpt;

    /**
     * Boolean flag whether it's a scalar KPI or not
     * 
     * The isset of this is also used to determine whether the kpi is 
     * configured properly
     * @var bool
     */
    protected $scalarMode;

    /**
     * The color ranges of the gauge component
     * @var ColorRange
     */
    protected $colorRanges;


    /**
     * The current value
     * @var int
     */
    protected $currentValue;

    /**
     * The current value options
     * @var KPIValueOptions
     */
    protected $currentOpts;

    /**
     * The past values of the KPI Component
     * @var array
     */
    protected $pastValues;

    /**
     * The Values of the target
     * 
     * @var array
     */
    protected $target;

    /**
     * A list of target names
     * 
     * @var array
     */
    protected $targetName;

    protected $sparkLineOpt;

    /**
     * Store the raw SQL query which will be executed 
     * @var string
     */
    protected $valueFromQuery;

    /**
     * The options for value from query
     * @var KPIValueFromQueryOptions
     */
    protected $valueFromQueryOpt;

    /**
     * @var KPIComponentOptions
     */
    protected $options;

    /**
     * The value expression
     * @var DBExpression
     */
    protected $valExp;

    /**
     * The value options
     * @var KPIValueOptions
     */
    protected $valOpt;

        protected function initialize() {
            $init = parent::initialize();
            if($init)
                return $init;

            // TODO: Set color Ranges, Target, and sparklineopt as part of Options
            $this->log("Initializing KPI Component");

            // create an array to hold the records
            $records = array();
            // a variable to hold the value options
            $valOpt = null;

            if(isset($this->currentValue))
            {
                $this->log("Treating as a scalar KPI");
                if(!isset($this->pastValues))
                {
                    $this->setOption('showLatestOnly', true);
                }

                if(isset($this->pastValues))
                {
                     for($i = count($this->pastValues) - 1; $i >= 0; $i --)
                     {
                         $records []= array(
                             'label' => "Record $i",
                             'value' => $this->pastValues[$i]
                         );
                     }
                }
                $records []= array(
                    'label' => 'current',
                    'value' => $this->currentValue
                );
                $valOpt = $this->currentOpts;
            }
            else if(isset($this->valExp)) {
                $this->log("Treating as a dynamic KPI");
                $valOpt = $this->valOpt;

                if(!isset($this->tsExp))
                {
                    if($this->valOpt->countDistinct)
                    {
                        $this->setOption('showLatestOnly', true);
                        $this->log("Doing a count distinct");

                        $this->query['select'] = array(
                            array(
                                'name' => "COUNT(DISTINCT ".$this->valExp->toString()." )",
                                'alias' => 'value'
                            )
                        );
                        $this->limitTo(1);
                    }
                    else {
                        $this->log("No timestamp expression. using a single value expression");

                        if($this->valOpt->aggregate)
                        {
                            // have to aggregate the values
                            $this->query['select'] = array(
                                array(
                                    'name' => $this->valExp->toString(),
                                    'func' => $this->valOpt->aggregateFunction,
                                    'alias' => 'value'
                                )
                            );
                        }
                        else {
                            $this->query['select'] = array(
                                array(
                                    'name' => $this->valExp->toString(),
                                    'alias' => 'value'
                                )
                            );
                        }

                        // check if the value needs to be grouped
                        if(isset($this->valOpt->groupBy))
                        {
                            $this->query ['group_by'] = array(
                                $this->valOpt->groupBy
                            );
                        }

                        $this->limitTo($this->valOpt->limitTo);
                    }

                    $data = $this->RunQuery(DataSource::__objToQuery($this->query));

                    // add the item to the records
                    // $records []= array(
                    //     'label' => 'default',
                    //     'value' => $data[0]['value']
                    // );
                    // the first record is taken as the current
                    $records []= array(
                        'label' => 'default',
                        'value' => $data[0]['value']
                    );

                    if(count($data) === 1)
                    {
                        $this->setOption('showLatestOnly', true);
                    }

                    for($i = 1; $i < count($data); $i ++)
                    {
                        $records []= array(
                            'label' => "Record $i",
                            'value' => $data[$i]['value']
                        );
                    }
                }
                else {
                    // Timestamp expression has been set
                    $this->log("Building KPI data with timestamp");

                    // ensure sorting in descending order
                    $this->sortOn($this->tsExp, "DESC");

                    // if there isn't any aggregation
                    if($this->valOpt->aggregate === false)
                    {
                        $this->log("Handling KPI with no aggregation");

                        $this->tsExp->alias = "label";
                        $this->valExp->alias = "value";

                        $this->query['select'] = array(
                            array(
                                'name' => $this->tsExp->toString(),
                                'alias' => $this->tsExp->getAlias()
                            ),
                            array(
                                'name' => $this->valExp->toString(),
                                'alias' => $this->valExp->getAlias()
                            )
                        );

                        $this->limitTo($this->valOpt->limitTo);

                        $records = $this->RunQuery(DataSource::__objToQuery($this->query));
                    }
                    else {
                        $this->log("Building KPI data using aggregated timestamp");

                        // configure the range system
                        $this->rangeCol = $this->tsExp;
                        $this->rangeColType = _RF_DTYPE_TIME;
                        $this->timeRangeType = $this->tsOpt->timeUnit;

                        // do an in-place range for timestamp. and add the CASE items to the SELECT
                        $rangeExp = $this->InPlaceRange();
                        $rangeExp['alias'] = 'label';
                        $this->query['select'] = array($rangeExp);

                        $this->query['select'] []= array(
                            'func' => $this->valOpt->aggregateFunction,
                            'name' => $this->valExp->toString(),
                            'alias' => 'value'
                        );
                        $this->query['group_by'] = array(
                            'label'
                        );

                        // Apply a limit
                        $this->limitTo($this->valOpt->limitTo);

                        $records = $this->RunQuery(DataSource::__objToQuery($this->query));
                    }
                }
            }
            else if (isset($this->valueFromQuery)) {
                $this->log("Building KPI data using a query");
                $data = $this->RunQuery($this->valueFromQuery);

                if(isset($data[0])) {
                    $valueCol = array_keys($data[0]);
                    // By default, assume that the valuecol is the first column
                    $valueCol = $valueCol[0];
                    if(isset($this->valueFromQueryOpt->valueColumn)){
                        $valueCol = $this->valueFromQueryOpt->valueColumn;
                    }

                    $val = number_format($data[0][$valueCol]);

                    // populate the current item
                    $records [] = array(
                        'label' => "Latest",
                        'value' => $val
                    );

                    if($this->valueFromQueryOpt->numberOfPreviousValues === 0) {
                        $this->setOption('showLatestOnly', true);
                    } else {
                        $numValues = $this->valueFromQueryOpt->numberOfPreviousValues + 1> count($data) ? count($data) : $this->valueFromQueryOpt->numberOfPreviousValues + 1;
                        for($i = 1; $i < $numValues; $i ++) {
                            $val = number_format($data[$i][$valueCol]);

                            // populate the current item
                            $records [] = array(
                                'label' => "Record $i",
                                'value' => $val
                            );
                        }
                        $records = array_reverse($records);
                    }

                    $valOpt = $this->valueFromQueryOpt;
                }
                else {
                    RFAssert::Exception("Your query " . $this->valueFromQuery . " returned 0 rows. It should at-least return 1");
                }
            }
            else {
                // TODO: Warn the user that they haven't set anything
                $valOpt = new KPIValueOptions ();

                $records []= array(
                    'label' => 'current',
                    'value' => "Unset" 
                );
            }

            $this->setOption('__targets', $this->targets);

            // Write the data records into the columnoptions
            $this->dataset['columnOptions']['value'] = $valOpt;
            $this->dataset['columnOptions']['label'] = new ColumnOptions(array(
                'type' => 'string',
                'name' => "Labels"
            ));
            $this->dataset['data'] = $records;

            return false;
    }



    /**
     * @ignore
     * @return string
     */
    public function getObjectType()
    {
        return __CLASS__;
    }

    public function __construct()
    {
        parent::__construct();
        $this->chromeless = true;
    }
}

class KPIComponentOptions extends ComponentOptions {
    /**
     * @internal
     * @ignore
     *
     * How to display this KPI Component
     * 
     * @var string
     */
    public $__displayMode = "default";

    /**
     * Should the KPI Component show only the latest value
     * even when past data is available?
     *
     * If true, it shows only the latest value
     * 
     * @var boolean
     */
    public $showLatestOnly = false;

    /**
     * In KPIs displayed as gauges, this value sets the lower
     * limit of the gauge
     * 
     * Note that in the event that the value of the gauge exceeds
     * the set loer limit, the value will be ignored.
     * 
     * @var number
     */
    public $lowerLimit;

    /**
     * In KPIs displayed as guages, this value sets the upper
     * limit of the gauge.
     *
     * Note that in the event that the value of the gauge exceeds
     * the set upper limit, the value will be ignored.
     * 
     * @var number
     */
    public $upperLimit;

    /**
     * The number of decimals to show while displaying the value
     * of the KPI.
     * 
     * @var integer
     */
    public $numberDecimals;

    /**
     * A string that is prefixed to the number on the KPI when it
     * is displayed on the screen.
     * 
     * @var string
     */
    public $numberPrefix;

    /**
     * Holds targets
     * 
     * @var numeric
     */
    public $__targets = array();


    /**
     * A :php:class:`ColorRange` object which will be used to change
     * the colors in the dial of the gauge.
     * 
     * @var ColorRange
     */
    public $colorRanges;

    public function __inheritedClassName ()
    {
        return __CLASS__;
    }
}

class KPITimestampOptions extends RFOptions{

    /**
     * A Calendar unit of time used for ranging and grouping
     * values. Absolute Date/Time values will be converted into
     * strings which match this time unit.
     *
     * Allowed Values: "year", "month", "day", "hour", "minute", "second"
     * 
     * @var string
     */
    public $timeUnit = "month";


    public function __inheritedClassName()
    {
        return __CLASS__;
    }

    public function getIgnoreList() {
        return array('timeUnit');
    }

}


class KPIValueOptions extends ColumnOptions{
    /**
     * @internal Override the auto detection of data type, since
     *           We know it'll always be a number
     *           
     * @var string
     */
    public $type = "number";

    public $valueFontSize = 40;

    /**
     * Combine several records of data into single records.
     *
     * For example, if you have a list of sales records, you
     * can aggregate it to determine the Average or Total sales.
     * 
     * @var bool
     */
    public $aggregate = false;

    /**
     * If you are combining several records using the ``aggregate``
     * option, then you can use ``groupBy`` to specify how the records
     * should be grouped.
     *
     * For instance, if you have a list of sales records with different
     * categories, you can set ``groupBy`` as the category, and a large
     * sales table gets condensed into a concise list of the total/average/etc
     * sales amount for each category.
     * 
     * @var string
     */
    public $groupBy;

    /**
     * Use ``countDistinct`` to count the number of distinct occurences
     * of the value expression.
     *
     * @var bool
     */
    public $countDistinct = false;

    /**
     * The SQL Function that is used for aggregation.
     *
     * Please consult the manual of your database provider for a
     * full list of aggregate functions. However, commonly supported
     * functions are:
     *
     * * "SUM"
     * * "AVG"
     * * "MIN"
     * * "MAX"
     * * "STDDEV"
     * 
     * @var string
     */
    public $aggregateFunction = "SUM";

    /**
     * In case there is data available for previous values of the KPI
     * use the ``limitTo`` option to specify the maximum number of
     * values to take and display on the screen.
     * 
     * @var int
     */
    public $limitTo = 15;

    public function getIgnoreList() {
        return array('groupBy', 'aggregateFunction', 'aggregate', 'countDistinct', 'limitTo');
    }

    public function __inheritedClassName()
    {
        return __CLASS__;
    }
}


class KPIValueFromQueryOptions extends KPIValueOptions {

    /**
     * The first row of the result of the query will be taken as the KPI value
     * 
     * You can optionally show data from previous rows as past values of the KPI
     * 
     * @var int 
     */
    public $numberOfPreviousValues = 0;

    /**
     * If your query returns rows multiple columns, the first one will be considered as 
     * the value. If you want a specific column to be requested as the value, set the
     * valueColumn option.
     * 
     * @var string
     */
    public $valueColumn;



    public function __inheritedClassName()
    {
        return __CLASS__;
    }

    public function getIgnoreList() {
        return array('groupBy', 'aggregateFunction', 'aggregate', 'countDistinct', 'limitTo', 'valueColumn', 'numPreviousValues');
    }

}

class SparkLineOptions extends RFOptions {
    public $type = "Line";

    public function __inheritedClassName() {
        return __CLASS__;
    }
}
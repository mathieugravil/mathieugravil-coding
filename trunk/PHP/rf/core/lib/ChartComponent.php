<?php


/**
 * This class contains all the functionality to create and control chart components.
 *
 * Please see :doc:`components/chart` for information on how to use this class.
 *
 * See :ref:`components-intro` for an introduction on how to use components.
 */
class ChartComponent extends Component
{
    /**
     * Set the label expression for DataSource-backed charts 
     *
     * The label expression is a SQL Expression which is used to find the
     * labels on the chart to display (The labels are on the X-Axis)
     *
     * For example, in a chart to show "Sales for each category" in a Database
     * table containing sales, the distinct values from "category" column will be 
     * shown on the X-Axis. This means that "category" is the Label Expression
     *
     * @param string $name The name of the X-Axis
     * @param DBExpression $labelExpression The label expression
     * @param ChartLabelOptions $options The options for the chart labels
     */
    public function setLabelExpression($name, $labelExpression, $options = array())
    {
        $this->debug("Setting label column ", $labelExpression, $options);

        $this->assertDataSourceLinked();
        
        // add to cache invalidators
        $args = func_get_args();
        $this->invalidate($args);
        
        $this->dbLabelExp = $labelExpression;



        $opt = new ChartLabelOptions($options);
        $opt->name = $name;

        $this->labelOpt = $opt;

        if($opt->autoDrill)
        {
            $this->enableAutoRangeDrilldown();
        }
        else if($opt->drillPath)
        {
            $this->setDeepDrill($opt->drillPath);
        }

        if(isset($opt->customTimeUnitPath))
        {
            $this->customTimeRangeOrder = $opt->customTimeUnitPath;
        }
    }
    
    
    /**
     * Add a series to a DataSource-backed Chart. You can add multiple series
     * to a chart by calling this function again.
     *
     * The Series Expression is a SQL Expression which corresponds to a numeric
     * expression which will be displayed on the chart.
     *
     * For example, if you want to show a chart of "Sales by Category" in a 
     * sales table, then you can use the column containing "Sales" as the series
     * expression.
     *
     * **NOTE**: the series expressions passed to this function are aggregated
     * by default. To disable aggregation, set ``'aggregate'=>false`` in the options
     *
     * @param string $name The name of the series
     * @param DBExpression $expression The series expression
     * @param  ChartSeriesOptions $options The options for the series
     *
     */
    public function addSeries($name, $expression, $options = array())
    {
        $this->scalarChart = false;

        $this->debug("Adding a series - $name", $expression, $options);
        $this->assertDataSourceLinked();
        
        $opt = new ChartSeriesOptions($options);
        
        // add args to cache invalidators
        $args = func_get_args();
        $this->invalidate($args);
        
        $exp = DBExpression::__consume($expression, $this);
        $this->dbSeriesList ['series_' . $this->seriesCount] = array(
            'name' => $name,
            'exp' => $exp,
            'opt' => $opt
        );
        $this->seriesCount++;
    }
    
    /**
     * Add a series to a static chart. You can add multiple series by calling
     * this function more than once.
     *
     * The data points of a series have to be a PHP array of numeric values,
     * which will be displayed on the chart as a series. Please ensure
     * that the number of data points in each series is the same as the number of
     * labels set with ``setStaticLabels``
     *
     * @param string $name The name of the series to be displayed in the legend
     * @param array $dataPoints An array of numeric values for the series
     * @param SeriesOptions $options Options for the series
     * 
     */
    public function addStaticSeries($name, $dataPoints, $options= array())
    {
        RFAssert::IsArray($dataPoints);
        $this->_assertDataSourceNotLinked();
        
        // don't cache scalar charts
        $this->setCaching();
        
        $this->scalarChart = true;
        
        $data = array();
        
        // iterate through all the data points again into   the array.
        // Why? Because we will later access data points by numerical index.
        // If the user has created an array and sorted it by passing it to this function,
        // the indices will be garbled up
        
        // also, if it's a key-value array, we'll need to convert it to a numerically indexed array
        
        foreach ($dataPoints as $key => $val)
        {
            $data [] = $val;
        }
        
        $opt = new ChartSeriesOptions($options);
        $opt->name = $name;
        
        $this->seriesArray [] = array(
            'dataPoints' => $data,
            'opt' => $opt
        );
    }
    
    /**
     * Set the labels for a static chart.
     *
     * **Note:** Please ensure that the number of labels is the 
     * same as the number of datapoints in each series
     * 
     * @param sting $name The name of the X-Axis
     * @param array $labels A PHP array of labels
     * @param ChartLabelOptions $labelArrayOptions Options for the label
     */
    public function setStaticLabels($name, $labels, $labelArrayOptions = array())
    {
        RFAssert::IsArray($labelArrayOptions);
        
        $this->_assertDataSourceNotLinked();

        $this->labelOpt = new ChartLabelOptions($labelArrayOptions);
        $this->labelOpt->name = $name;
        
        // Don't cache scalar charts
        $this->setCaching();
        $this->scalarChart = true;
        $this->labelArray = $labels;
    }

    /**
     * Set the name and options for the Y-Axis of the Chart
     * 
     * @param string $name The name of the Y-Axis
     * @param ChartAxisOptions $options Options for this axis
     */
    public function setYAxis ($name, $options = array())
    {
        $args = func_get_args();
        $this->invalidate($args);

        $opt = new ChartAxisOptions($options);
        $opt->__name = $name;
        $this->properties['yAxis'] = $opt->asArray();
    }

    /**
     * Create and configure a secondary Y-Axis for use in a chart.
     *
     * Note that in order for a series to use this axis, you need to specify
     * that the second Y-Axis must be used inside the Series Options.
     *
     * For example ::
     *
     *     $chart->addSeries("Sales", "Table.SalesAmount", array('onSecondYAxis' => true))
     *
     * @param string $name Name of the second Y-Axis
     * @param ChartAxisOptions $options Options for this Axis
     */
    public function setSecondYAxis($name, $options = array())
    {
        $args = func_get_args();
        $this->invalidate($args);

        $opt = new ChartAxisOptions($options);

        $opt->__name = $name;
        $this->properties['secondYAxis'] = $opt->asArray();
    }
    
    /**
     * Perform an action when a series is clicked inside the chart. Register a callback
     * function with $callback, which can be used to modify the target.
     *
     * @param function $callback
     * @param Component $target
     * @param array|EventOptions $eventOptions
     */
    public function onSeriesClicked($callback, $target, $eventOptions = array())
    {
        $this->registerAction('seriesClicked', $callback, $target, $eventOptions);
    }
    
    
    /**
     * Set the Deep Drill path
     *
     * @todo: Rename
     * @param array $drillList
     */
    protected function setDeepDrill($drillList)
    {
        $this->hasActionsFlag = true;
        $this->assertDataSourceLinked();

        $this->deepDrill = $drillList;
        $this->properties['needsBreadCrumbs'] = true;
    }

    /**
     * Enables auto range drilldown
     *
     * @todo: Rename this function
     */
    protected function enableAutoRangeDrilldown ()
    {
        $this->hasActionsFlag = true;
        $this->autoRangeDrill = true;
        $this->properties['needsBreadCrumbs'] = true;
    }
    

    /**
     * AutoLink this chart to another component. When a series on this chart is clicked, 
     * The target component will be filtered automatically according to the series and 
     * label that the user selected.
     * 
     * @param  Component $target The component to link to.
     * @param  EventOptions $options Options for the auto link
     */
    public function autoLink($target, $options = array())
    {
        $this->simpleRegisterAction($target, $options);
        $target->__registerDrillParent($this);
        $this->__internalAutofilter($target);
    }

    /**
     * This is a internal RazorFlow function.
     *
     * @internal
     */
    public function __internalAutofilter($target)
    {
        $this->hasActionsFlag = true;
        $this->drillTargets []= $target;
    }

    /**
     * Add a Trend Line to the Chart. Trend lines are horizontal lines spanning the chart canvas
     * which aid in interpretation of data with respect to some pre-determined value.
     * 
     * @param string $name The name of the Trendline
     * @param numeric $value The value at which the trendline is displayed
     * @param string $color='auto' The color, in Hexadecimal sring format, without a # in the beginning
     */
    public function addTrendLine($label, $value, $color='auto')
    {
        $this->trendLines []= array(
            'name' => $label,
            'value' => $value,
            'color' => $color
        );

    }


    protected $drillTargets = array();
    
    
    /**
     * Flag to signify whether this is a scalar chart
     * @var bool
     */
    protected $scalarChart = true;
    
    /**
     * The label array if this is a scalar chart
     *
     * @var array
     */
    protected $labelArray = array();
    
    /**
     * The list of series if this is a sclar chart
     *
     * @var array
     */
    protected $seriesArray = array();
    
    /**
     * The label column of the linked data source
     *
     * @var string
     */
    // protected $dbLabelColumn;

    /**
     * The actual expression used to get the labels
     * @var string
     */
    protected $dbLabelExp;
    
    /**
     * The series list descriptors if the chart derives data from a DataSource
     *
     * @var array
     */
    protected $dbSeriesList = array();
    
    /**
     * An internal counter used to get the keys of the chart
     *
     * @var int
     */
    protected $seriesCount = 0;
    
    /**
     * A flag for auto range drill. Will cause automatic drilldown when charts are clicked
     * if it is set to true
     *
     * @var bool
     */
    public $autoRangeDrill = false; // TODO: Rename this to something else
    
    /**
     * The deep drill path
     *
     * @var array
     */
    protected $deepDrill = array();
    
    /**
     * The list of filter targets
     *
     * @var array
     */
    protected $autoFilterTargets = array();

    /**
     * An array to hold all the trendlines
     * @var array
     */
    protected $trendLines = array();

    /**
     * The label options
     * @var ChartLabelOptions
     */
    protected $labelOpt;

    /**
     * @var array
     */
    protected $customTimeRangeOrder;


    /**
     * This function is an alternate initialize() which is called
     * from intitialize if it is a scalar chart.
     *
     * @return bool
     */
    protected function handleScalarChart()
    {
        // do a sanity check
        $labelCount = count($this->labelArray);
        $seriesCount = count($this->seriesArray);
        
        foreach ($this->seriesArray as $series)
        {
            if (count($series['dataPoints']) !== $labelCount)
                RFAssert::Exception("Inconsitent number of data points");
        }
        
        // populate the data series
        for ($i = 0; $i < $labelCount; $i++)
        {
            $row = array();
            $row["rfLabels"] = $this->labelArray [$i];
            for ($j = 0; $j < $seriesCount; $j++)
            {
                $row["series_$j"] = $this->seriesArray[$j]['dataPoints'][$i];
            }
            $this->dataset['data'] []= $row;
        }
        
        $index = 0;
        foreach ($this->seriesArray as $series)
        {
            $this->dataset['columnOptions']["series_$index"] = $series['opt']->asArray();
            $index++;
        }
        
        $this->properties['dbLabelColumn'] = 'rfLabels';
        $this->dataset['columnOptions']["rfLabels"] = array();

        if(isset($this->labelOpt))
            $this->dataset['columnOptions']["rfLabels"] = $this->labelOpt->asArray();
        
        return false;
    }

    protected function handleNoAggChart ()
    {
        RFAssert::Exception("Not implemented");
/*        $dbLabelColumn = $this->dbLabelColumn;
        $this->query['select'] = array('exp' => array(
            'name' => $this->dbLabelExp,
            'alias' => $dbLabelColumn
        ));

        $index = 0;
        foreach($this->dbSeriesList as $key => $series)
        {
            $exp = $series['exp'];
            $this->query['select']['exp'] []= array(
                'name' => $exp->toString(),
                'alias' => "series_$index"
            );
            $index ++;
        }

        $this->dataset->data = $this->RunQuery(obj_to_query($this->query));*/

    }

    protected function handleAggChart() {
        $aggFlag = true;
        $rangeFlag = false;
        $labelType = "text";

        if(isset($this->labelOpt))
        {
            if(isset($this->labelOpt->timestampRange))
            {
                $rangeFlag = true;
                $labelType = _RF_DTYPE_TIME;
            }
        }
        
        $labelExp = DBExpression::__consume($this->dbLabelExp, $this);

        $distinctLabelExp = $labelExp->toString();

        if ($rangeFlag) {
            if($labelType === _RF_DTYPE_TIME)
            {
                if(!isset($this->timeRangeType))
                {
                    $this->timeRangeType = $this->labelOpt->timeUnit;
                }
                else
                {
                    // Override the timeunit with the range type
                    // this will be activated on drilldowns
                    $this->labelOpt->timeUnit = $this->timeRangeType;
                }
            }
            else {
                RFAssert::Exception("Only 'time' range type is supported");
            }
            
            $this->rangeCol = $this->dbLabelExp;
            $this->rangeColType = $labelType;
            $rangeExp = $this->InPlaceRange();
            
            // hack the alias to say "_range" for now
            $rangeExp['alias'] = 'rfLabels';

            // We can fetch the distinct labels using the "name" parameter of the rangeExp
            $distinctLabelExp = $rangeExp['name'];

            $this->query['select'] = array(
                $rangeExp
            );

            // modify the chart label options
            $this->labelOpt->__ranged = true;
            $this->labelOpt->__fillMode = 1;
            $this->labelOpt->type = "date";
            $this->labelOpt->timeFormatString = $this->GetNumberFormatter($this->timeRangeType);
        }
        if(!isset($this->query['select']))
            $this->query['select'] = array(
                array(
                    'name' => $this->dbLabelExp,
                    'alias' => 'rfLabels'
                )
            );
        
        $uniqueLabels = array();
        if (true) {
            $labelQuery = $this->query;
            $labelQuery['select'] = array();
            $labelQuery['select'] []= array(
                'type' => 'func',
                'func' => 'DISTINCT',
                'name' => $distinctLabelExp,
                'alias' => 'rfLabels'
            );
            
            $labelItems = $this->RunQuery(DataSource::__objToQuery($labelQuery));
            
            foreach ($labelItems as $row)
            {
                $uniqueLabels [] = $row['rfLabels'];
            }
        }
        else
        {
            // There has been a range. Pick up the labels in the correct order

        }
        
        // construct a blank table for all the values
        $dataTable = array();
        foreach ($uniqueLabels as $label)
        {
            $dataTable[$label] = array();
            foreach ($this->dbSeriesList as $key => $series)
            {
                $dataTable[$label][$key] = 0;
            }
        }
        
        
        foreach ($this->dbSeriesList as $key => $series)
        {
            $sQuery = $this->query;

            // get the options for the series.

            /** @var $opt ChartSeriesOptions */
            $opt = $series['opt'];

            /** @var $dbExp DBExpression */
            $dbExp = $series['exp'];

            // Get the select expression.
            $sSelectExp = $dbExp->toString();

            // Do we need to aggregated?
            if ($aggFlag) {
                $aggFunc = "SUM";

                if(isset($opt->aggregateFunction))
                {
                    $aggFunc = $opt->aggregateFunction;
                }


                $sSelectExp = array(
                    'func' => $aggFunc,
                    'name' => $sSelectExp,
                    'alias' => $key
                );
            }
            
            $sQuery['select'] []= $sSelectExp;

            if(is_array($labelExp)) {
                if(isset($labelExp["alias"])) {
                    unset($labelExp['alias']);
                }
            }

            $sQuery['group_by'] = array(
                'rfLabels'
            );
            
            if (isset($opt->condition)) {
                /** @var $conditionExp DBExpression */
                $conditionExp = DBExpression::__consume($opt->condition, $this);

                $conditionString = $conditionExp->toString();

                if (!isset($sQuery['where']))
                    $sQuery['where'] = array();
                
                $sQuery['where'] [] = array(
                    'cond' => $conditionString,
                    'bool' => "AND"
                );
            }
            
            $seriesData = $this->RunQuery(DataSource::__objToQuery($sQuery));
            
            foreach ($seriesData as $row)
            {
                $label = $row['rfLabels'];
                $value = $row[$key];
                
                if (!isset($dataTable[$label]))
                    RFAssert::Exception("TODOERROR: The label $label hasn't been seen before. This shouldn't happen");
                
                $dataTable[$label][$key] = $value;
            }

            if(count($this->deepDrill) > 0 || $this->autoRangeDrill === true)
            {
                $this->properties['drillMode'] = true;
            }
        }
        
        foreach ($dataTable as $key => $row)
        {
            $row['rfLabels'] = $key;
            $this->dataset['data'] []= $row;
        }

    }
    
    /**
     * The main entry point for the component.
     *
     * @return bool
     */
    protected function initialize()
    {
        $init = parent::initialize();
        $this->log("initializing chart component");
        if ($init)
            return $init;


        if(count($this->trendLines) > 0)
        {
            $this->setOption('_trendLines', $this->trendLines);
        }

        
        if ($this->scalarChart)
            return $this->handleScalarChart();

        if ($this->options->aggregate === false)
        {
            $this->handleNoAggChart();
        }
        else
        {
            $this->handleAggChart();
        }
        
        // // go through all the columns and see if any series is asking for a sort
        $sortNeeded = false;

        // create a global sortKey and sortMethod, which will be used in the sorting function
        global $sortKey, $sortMethod;
        $sortKey = ""; $sortMethod = "";

        foreach($this->dbSeriesList as $key => $series)
        {
            $opt = $series['opt'];
            if(isset($opt->sort)) // if a sort has been set
            {
                $sortKey = $key;
                $sortMethod = $opt->sort;
                $sortNeeded = true;

                break; // let's start the sorting routine right away
                // TODO: If more than one column has a sort set, it'll ignore all but
                // TODO: the first one
            }
        }

        if($sortNeeded)
        {
            if(!function_exists('chartSortFunc'))
            {
            function chartSortFunc ($a, $b)
            {
                global $sortKey, $sortMethod;
                if($a[$sortKey] === $b[$sortKey])
                    return 0;
                if($sortMethod === "ASC")
                {
                    return $a[$sortKey] <= $b[$sortKey] ? 1 : -1;
                }
                else
                {
                    return $a[$sortKey] >= $b[$sortKey] ? -1 : 1;
                }
            }

            }

            usort($this->dataset['data'], 'chartSortFunc');
        }

        if(isset($this->options->limit))
        {
            $this->dataset['data'] = array_slice($this->dataset['data'], 0, $this->options->limit);
        }

        
        foreach ($this->dbSeriesList as $key => $series)
        {
            /** @var $colOpt ChartSeriesOptions */
            $colOpt = $series['opt'];
            $colOpt->name = $series['name'];
            $colOpt->type = "number";

            $this->dataset['columnOptions'][$key] = $colOpt->asArray();
        }

        $this->dataset['columnOptions']['rfLabels'] = $this->labelOpt->asArray();

        // set the label column in the properties, used for the deep drills
        $this->properties['dbLabelColumn'] = 'rfLabels';

        return false;
    }

    protected function onActionTriggered($actionName, $params)
    {
        $this->debug("Action $actionName has been triggered w/ params", $params);

        $allDrillTargets = array();

        if(count($this->drillParents) > 0)
        {
            // go to each of the drill parents and add all the children to the drill 
            // source's path of destruction
            // 
            foreach($this->drillParents as $parent)
            {
                foreach($this->drillTargets as $target)
                {
                    $parent->__internalAutofilter($target);
                }
            }
        }

        // Check if we are doing a deep drill
        if (count($this->deepDrill) > 0)
            $allDrillTargets = array_merge($allDrillTargets, $this->doDeepDrill($params));

        // check whether to do a range drill. Or if there are no drill targets
        if ($this->autoRangeDrill)
        {
            $allDrillTargets = array_merge($allDrillTargets, $this->doRangeDrill($params));
        }

        if (count($this->drillTargets) > 0)
        {
            $allDrillTargets = array_merge($allDrillTargets, $this->doAutoFilter($params));
        }

        return $allDrillTargets;
    }

    public function getOptionClassName()
    {
        return "ChartComponentOptions";
    }


    /**
     * Do the range drill.
     *
     * @param $params
     * @return array
     */
    protected function doRangeDrill($params)
    {
        $this->loadOptions();
        if (!$this->autoRangeDrill)
            return array();

        if (!isset($params['timeUnit']))
        {
            return array(); // TODO: Implement range drills on numeric ranges
        }



        $timeRangeType = $params['timeUnit'];
        $timeRangesList = array("year", "month", "day", "hour", "minute", "second");

        if(isset($this->customTimeRangeOrder))
        {
            $timeRangesList = $this->customTimeRangeOrder;
        }
        $currentIndex = array_search($timeRangeType, $timeRangesList);

        if ($currentIndex === FALSE) {
            RFAssert::Exception("Error! unknown timerange type. it's not there in timeRangesList");
            return array();
        }
        if ($currentIndex === count($timeRangesList) - 1) {
            RFLog::log("Reached the maximum depth of time Range drilldown");
            return array();
        }

        // configure the ranging system, so we can get a list of conditions, and we can determine
        // the appropriate condition to add for this deep drill
        $this->rangeCol = $this->dbLabelExp;
        $this->rangeColType = _RF_DTYPE_TIME;
        $this->timeRangeType = $timeRangeType;

        $cond = new DBExpression($this->GetTimeFormatQuery($this->rangeCol, $this->timeRangeType), '=', $params['label']);
        $cond->__setlds($this);

        // override the time range type with the new one
        $this->timeRangeType = $timeRangesList [$currentIndex + 1];

        // add a "where" condition, so only appropriate dates are selected
        $this->addSQLWhere($cond);

        return array($this);
    }

    /**
     * @internal
     * 
     */
    protected function doDeepDrill($params) {
        $currentLevel = 0;

        $drillState = array(
            'currentLevel' => 0,
            'pastValues' => array()
        );

        if(isset($params['drillState']))
        {
            $drillState = $params['drillState'];
        }

        $currentLevel = $drillState['currentLevel'];

        $value = "";
        if(!isset($params['value']))
        {
            RFAssert::Exception("Invalid data, missing value from params");
        }
        $value = $params['value'];

        if($currentLevel + 1 >= count($this->deepDrill)) {
            return array();
        }

        $nextLevel = $currentLevel + 1;

        // Determine the next and current label expressions
        $nextLabelExp = $this->deepDrill[$nextLevel];
        $curLabelExp = $this->deepDrill[$currentLevel];

        // add the current value into the pastValues, so it's handled cleanly
        $drillState['pastValues'] []= $value;

        // re-apply the past conditions
        for($i = 0; $i < count($drillState['pastValues']); $i ++)
        {
            $drillVal = $drillState['pastValues'][$i];

            if(empty($drillVal) || $drillVal === "null") {

                // Null strings are converted into empty 
                $this->addCondition($this->dataSource->__ifnullexp($this->deepDrill[$i], "''"), '=', "");
            }
            else {
                $this->addCondition($this->deepDrill[$i], '=', $drillVal);
            }
        }

        // Make sure that initalize() uses the new label expression
        $this->dbLabelExp = $nextLabelExp;

        // make sure that the current level is set so it's sent back next time
        $drillState['currentLevel'] = $nextLevel;
        $this->properties['drillState'] = $drillState;

        return array($this);
    }


    protected function doAutoFilter ($params)
    {
        $this->debug("Doing an autofilter");
        $label = $params['label'];
        $rangeFlag = false;
        $labelType = _RF_DTYPE_TEXT;
        $timeRangeType = $params['timeUnit'];

        if(isset($this->labelOpt))
        {
            if(isset($this->labelOpt->timestampRange))
            {
                $rangeFlag = true;
                $labelType = _RF_DTYPE_TIME;
            }
        }

        if (!$rangeFlag) {
            /** @var $target Component */
            foreach ($this->drillTargets as $target)
            {
            // TODO: Verify that the data source of the source and target are the same
                $target->addSQLWhere(new DBExpression($this->dbLabelExp, '=', $label));
            }
        }
        else // the only reasonable way to determine whether this is a ranged column
        {
            $this->debug("Need to do a drilldown based on a label from a ranged column");

            $timeRangeType = $params['timeUnit'];
            $this->rangeCol = $this->dbLabelExp;
            $this->rangeColType = $labelType;
            $this->timeRangeType = $timeRangeType;

            $cond = new DBExpression($this->GetTimeFormatQuery($this->rangeCol, $this->timeRangeType), '=', $label);
            $cond->__setlds($this);

            /**
             * @var $target Component
             */
            foreach ($this->drillTargets as $target)
            {
                $target->addSQLWhere($cond);
            }
        }

        return $this->drillTargets;
    }


    /**
     * @return string
     */
    public function getObjectType()
    {
        return "ChartComponent";
    }
}


class ChartComponentOptions extends ComponentOptions {
    /**
     * A container to hold the trend lines if any are set inside the code.
     * Please do not use this directly, and use ChartComponent::addTrendLine instead
     *
     * @var
     * @internal
     */
    public $_trendLines;

    /**
     * Should the values of the series be shown on the chart. By default
     * the values are shown.
     *
     * **Note:** If unset, it's automatically disabled for smaller screens to 
     * avoid clutter. If you set showValues to true, it will always show values.
     * 
     * @var bool
     */
    public $showValues;

    /**
     * Flag to show the labels on the chart.
     *
     * **Note:** If unset, it's automatically disabled for smaller screens to avoid clutter.
     * If you do set showLabels to true, it will always show the labels.
     * 
     * @var bool
     */
    public $showLabels;

    /**
     * Should the legend be displayed on the chart?
     * 
     * @var bool
     */
    public $showLegend = true;

    /**
     * The position of the legend on the chart.
     *
     * Available options:
     * 
     * * Bottom
     * * Right
     * 
     * @var string
     */
    public $legendPosition = 'bottom';

    /**
     * For internal use only.
     *
     * @var array
     */
    public $__funnelOrder;

    /**
     * For internal use only
     *
     * The current level of the drill down.
     * @var int
     */
    public $__currentLevel;


    /**
     * Maximum number of items to show on the X-Axis.
     * 
     * @var int
     */
    public $limit;




    /**
     *
     * @internal
     * @todo this needs to be removed
     */
    public $aggregate = true;

    public function __inheritedClassName ()
    {
        return __CLASS__;
    }
}

class ChartLabelOptions extends ColumnOptions{
    public $name = "Labels";

    // Override the type to be string by default.
    // If it's a ranged column, it will be set to date
    public $type = "string";

    /**
     * Activate automatic drill-down time range charts, when your label expression
     * corresponds to a SQL DATETIME type. Set to "true" to activate
     * 
     * @var bool
     */
    public $timestampRange;

    /**
     * Activate the _autoDrill_ feature, where the the user can drill down into
     * the chart by using different label columns.
     *
     * This requires the :php:attr:`ChartLabelOptions::$drillPath` option to be
     * set.
     * 
     * @var bool
     */
    public $autoDrill = false;

    /**
     * A Calendar unit of time used for ranging and grouping
     * values. Absolute Date/Time values will be converted into
     * strings which match this time unit.
     *
     * Allowed Values: "year", "month", "day", "hour", "minute", "second"
     * 
     * @var string
     */
    public $timeUnit = "year";

    /**
     * The drill path to use while performing drill downs.
     * 
     * @var array
     */
    public $drillPath;

    /**
     * Use a different path of time units while performing drill downs. Only relavant
     * when timestampRange is active
     *
     * For example, if you want to show the "day" right after "month", you
     * can use set an option like this ::
     *
     *     array('timestampRange' => array('month', 'day'))
     * 
     * @var array
     */
    public $customTimeUnitPath;

    /**
     * Setting this to true will display empty values in a time series chart.
     * 
     * @var boolean
     */
    public $fillEmpty = true;


    public function __inheritedClassName()
    {
        return __CLASS__;
    }

    public function getIgnoreList(){
        return array('autoDrill', 'drillPath', 'customTimeUnitPath');
    }
}

class ChartSeriesOptions extends ColumnOptions 
{
    public $type = "number";

    /**
     * The display type of the series. Possible options are:
     *
     * 1. Column
     * 2. Line
     * 3. Bar
     * 4. Area
     * 5. Pie
     *
     * @var string
     */
    public $displayType = "Column";

    /**
     * The condition that
     *
     * @var string
     */
    public $condition;

    /**
     * A boolean flag to set to "true" if the series is to be measured
     * against the second y axis
     * @var boolean
     */
    public $onSecondYAxis;

    /**
     * Sort according to this series
     *
     * @var string
     */
    public $sort;

    /**
     * The aggregation function. For a list of supported aggregation functions, see:
     *
     * http://dev.mysql.com/doc/refman/5.0/en/group-by-functions.html [mysql]
     * http://www.sqlite.org/lang_aggfunc.html [sqlite]
     * http://msdn.microsoft.com/en-us/library/ms173454.aspx [SQL Server]
     *
     * @var string
     */
    public $aggregateFunction = "SUM";

    /**
     * The color of the series
     *
     * Set a hexadecimal string like "A4C9F3".
     *
     * * There should **not** be a "#" in the beginning of the string like in CSS.
     * * You can also use a valid `CSS Color Keyword <http://www.w3.org/TR/CSS21/syndata.html#color-units>`_
     * 
     * @var string
     */
    public $color;

    public $showValues;

    public function __inheritedClassName ()
    {
        return __CLASS__;
    }

    /**
     * How to display the pie chart labels?
     *
     * Options:
     * 1. "value" // Show value as a numeric value
     * 2. "percentage" // Show value as a percentage of the total chart
     *
     * **Note**: If showValues has been set to false, the values will not be shown
     * 
     * @var string
     */
    public $pieChartLabelDisplay;

    public function getIgnoreList()
    {
        return array('condition', 'aggregate');
    }
}

/**
 * This class contains options to modify the behavior of the Axes of
 * the charts.
 */
class ChartAxisOptions extends NumberFormatOptions {

    /**
     * @internal
     * Internal. Please ignore
     * 
     * @var string
     */
    public $__name;

    /**
     * Explicitly set the lower limit for this axis
     * 
     * @var string
     */
    public $minValue;

    /**
     * Explicitly set the upper limit for this axis 
     * 
     * @var string
     */
    public $maxValue;

    /**
     * If set to true, the lower limit of the axis will change to be a
     * non-zero figure based on the values of the chart.
     *
     * If it is set to false, 9 will be stored as the Y Axis minimum always
     * 
     * @var boolean
     */
    public $adaptiveYMin = false;

    public function __inheritedClassName() {
        return __CLASS__;
    }
}

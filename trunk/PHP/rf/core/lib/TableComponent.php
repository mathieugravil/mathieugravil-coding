<?php

class TableComponent extends Component {
    /**
     * Add a static column to the table. This method is used only to
     * specify information about the columns. To add data, you need to use
     * :php:meth:`TableComponent::addStaticRow` to add rows of data.
     *
     * The type of data in the column will be automatically detected but
     * you can also override the type in the options.
     *
     * @param string $colName The name of the column 
     * @param TableColumnOptions $options Options for the column
     */
    public function addStaticColumn ($colName, $options= array())
    {
        $opts = new TableColumnOptions($options);
        $opts->name = $colName;
        $this->scalarColumns []= array(
            'opts' => $opts
        );
    }

    /**
     * Add a static row of data to the table. You need to add columns for
     * each item in the array using :php:ref:`TableComponent::addStaticColumn`
     * 
     * The data must be an array of values of mixed types. The types will 
     * auto detected from
     * the data but you can also override them while specifying the column.
     * 
     * @param array $data An array of data to add
     */
    public function addStaticRow ($data) {
        RFAssert::IsArray($data);
        $this->scalarData []= $data;
    }


    /**
     * Create a column from a Database Expression.
     *
     * @param string $name The name of the column
     * @param string $exp A Database Expression
     * @param TableColumnOptions $options The column options
     */
    public function addColumn ($name, $exp, $options = array())
    {
        $this->scalarFlag = false;
        $this->assertDataSourceLinked();
        $args = func_get_args();

        $this->invalidate($args);

        $colExp = DBExpression::__consume($exp, $this);

        $opt = new TableColumnOptions($options);
        $opt->name = $name;

        $key = "col_".$this->nDbCols;
        $colExp->alias = $key;

        $this->dbCols [$key] =  array(
            'exp' => $colExp,
            'opt' => $opt
        );

        $this->nDbCols ++;
    }

    public function fetchColumn ($id, $exp, $options = array()) {
        $options = new TableColumnOptions($options);
        $options->__hidden = true;
        $options->__overrideID = $id;
        $this->addColumn("", $exp, $options);
    }

    public function addCustomHTMLColumn ($name, $callback, $options = array()) {
        $key = "col_".$this->nDbCols;

        $options = new TableColumnOptions($options);
        $options->name = $name;
        $options->__rawHTML = true;


        $this->dbCols [$key] =  array(
            'callback' => $callback,
            'opt' => $options
        );

        $this->nDbCols ++;
    }

    /**
     * An even that is fired when a Dashboard user clicks on a row.
     * 
     * Note that the event matters only on the row, and will be the same irrelavant of
     * the column that is clicked.
     * 
     * The following paramters are sent to the callback:
     * 
     * 1. rowId - The index of the row that is clicked
     * 2. colId - the index of the column that is clicked on
     * 3. value - The value of the cell that was clicked
     * 
     * @param  function $callback     The callback function to execute to handle the event
     * @param  RFComponent   $target       The target component. This should also be available on the same dashboard
     * @param  EventOptions|array    $eventOptions The event options
     */
    public function onRowClicked ($callback, $target, $eventOptions = array())
    {
        $this->properties['hasRowActions'] = true;

        $this->registerAction('rowClicked', $callback, $target, $eventOptions);
    }


    /**
     * An even that is fired when a Dashboard user clicks on a cell.
     * 
     * The following paramters are sent to the callback:
     * 
     * 1. rowId - The index of the row that is clicked
     * 2. colId - the index of the column that is clicked on
     * 3. value - The value of the cell that was clicked
     * 
     * @param  function $callback     The callback function to execute to handle the event
     * @param  RFComponent $target       The target component. This should also be available on the same dashboard
     * @param  EventOptions|array    $eventOptions The event options
     */
    public function onCellClicked ($callback, $target, $eventOptions = array())
    {
        $this->properties['hasCellActions'] = true;

        $this->registerAction('cellClicked', $callback, $target, $eventOptions);
    }

    /**
     * Automatically filter another component based on a user's interaction
     * with the table.
     *
     * @param  Component $target The target component
     * @param array $options
     * @return void
     */
    public function autoLink ($target, $options = array())
    {
        $this->simpleRegisterAction($target, $options);
        $target->__registerDrillParent($this);

        $this->properties['hasRowActions'] = true;
        $this->autoFilterTargets []= $target;
        $this->hasActionsFlag = true;
    }

    /**
     * Should we paginate? yes/no
     * @var boolean
     */
    protected $paginateFlag = true;

    /**
     * Does this component have any options?
     * @var boolean
     */
    protected $hasActionsFlag = false;

    /**
     * The number of pages to paginate through
     * @var integer 
     */
    protected $paginateCount = 10;

    /**
     * should pagination skip?
     * @var boolean
     */
    protected $paginateSkip = true;

    /**
     * The database column options. Used to store all the info about the DB Columns
     * @var array
     */
    protected $dbColOptions = array();

    /**
     * Is this table powered by scalar data? yes/no
     * @var boolean
     */
    protected $scalarFlag = true;

    /**
     * All the scalar columns are stored here.
     * @var array
     */
    protected $scalarColumns = array();

    /**
     * Scalar data points.
     * 
     * @var array
     */
    protected $scalarData = array();

    /**
     * A counter to store the number of database columns so far.
     * @var integer
     */
    protected $nDbCols = 0;

    /**
     * The auto filter targets
     * @var array
     */
    protected $autoFilterTargets = array();


    /**
     * the DataBase columns
     * @var array
     */
    protected $dbCols = array();

    /**
     * The custom filters
     * @var array
     */
    protected $customFilters = array();

    /**
     * The total number of records
     * @var integer
     */
    protected $total;

    /**
     * a flag which when set, initialize() will call data.
     *
     * @var boolean
     */
    protected $needsInit = true;

    protected function scalarInitialize ()
    {
        $colCount = count($this->scalarColumns);

//        RFAssert::Assert("There should be columns in the table", $colCount !== 0);

        $rowCount = count($this->scalarData);
        for($i = 0; $i < $rowCount; $i ++)
        {
            $row = array ();
            
            for($j= 0; $j < $colCount; $j ++)
            {
                $colKey = "column_$j";

                $row[$colKey] = $this->scalarData[$i][$j];
            }
            $this->dataset['data'] []= $row;
        }

        $colIndex = 0;
        foreach($this->scalarColumns as $column)
        {
            $colKey = "column_$colIndex";
            $this->dataset['columnOptions'] [$colKey] = $column['opts']->asArray();
            $colIndex ++;
        }

        $this->properties['scalar'] = true;

        return false; // don't stop the chain
    }

    protected function initialize() {
        if(parent::initialize())
            return true;

        // Since the real heavy lifting will be done by the getData RPC Call,
        // we will instead just set the columnOptions so the schema can be built
        if($this->scalarFlag)
        {
            // On the other hand, make sure we initialize it for scalars, since the 
            // data isn't loaded on an ajax call.
            $this->scalarInitialize();

        }
        else {

            // Now we don't necessarily need to init 
            if($this->needsInit)
            {
                $this->dataInitialize();
            }
            foreach($this->dbCols as $key => $col)
            {
                if($col['opt']->__hidden) {
                    // Don't show hidden columns
                    continue;
                }
                $this->dataset['columnOptions'] [$key] = $col['opt']->asArray();
            }
        }

        return false; // don't stop the chain
    }


    protected function dataInitialize()
    {
        if($this->scalarFlag)
            return $this->scalarInitialize();

        $this->query['select'] = array(
        );

        $aggFlag = false;
        $groupByExp = null;
        $setFirstColAsPrimaryFlag = true;
        $sortExp = null;
        $sortMethod = "";

        foreach($this->dbCols as $key => $col)
        {
            if(!isset($col['exp'])) {
                // For formatted columns
                continue;
            }
            /** @var $exp DBExpression */
            $exp = $col['exp'];

            /** @var $opt TableColumnOptions */
            $opt = $col['opt'];

            // Create a default selectItem in case there are no
            $selectItem = array(
                'name' => $exp->toString(),
                'alias' => $exp->alias
            );

            if($opt->primaryColumn === true)
            {
                // the user has already specified a proper primary column
                $setFirstColAsPrimaryFlag = false;
            }

            if($opt->aggregate === true)
            {
                $aggFlag = true; // there are aggregates

                $selectItem = array(
                    'func' => $opt->aggregateFunction,
                    'name' => $exp->toString(),
                    'alias' => $exp->alias
                );

                if($opt->groupBy === true)
                {
                    RFAssert::Exception("Cannot Group by a column that is being aggregated");
                }

                if($opt->displayAsRange)
                {
                    RFAssert::Exception("Cannot range and aggregate a single column simaltaneously");
                }
            }

            if($opt->displayAsRange)
            {
                RFAssert::InArray("The 'displayAsRange' parameter must be valid", $opt->displayAsRange, array(_RF_DTYPE_NUM, _RF_DTYPE_TIME));

                $this->rangeCol = $exp;
                $this->rangeColType = $opt->displayAsRange;
                $this->timeRangeType = $opt->timeUnit;

                $selectItem = $this->InPlaceRange();
                $opt->timeFormatString = $this->GetNumberFormatter($this->timeRangeType);

                // re-set the type of the column to date, so it's picked up by kendo's datasource
                if($opt->displayAsRange === _RF_DTYPE_TIME)
                {
                    $opt->type = 'date';
                    $opt->__ranged = true;
                }
                else
                    $opt->type = 'string';

                $this->dbCols[$key]['opt'] = $opt;
            }

            if($opt->groupBy === true)
            {
                $groupByExp = $selectItem['alias'];
            }

            if($opt->sort !== "")
            {
                RFAssert::InArray("The sort parameter should be valid", $opt->sort, array("ASC", "DESC"));

                if(!isset($opt->displayAsRange))
                {
                    // this isn't a range. Just sort by the column's alias
                    $sortExp = $selectItem['alias'];
                    $sortMethod = $opt->sort;
                }
                else
                {
                    // if it's a range, then just sort by the expression itself.
                    // This is because if we sort by the alias, it will try to sort strings.
                    // On the other hand, just sorting by the original expression will lead to proper results
                    
                    $sortExp = $exp->toString();
                    $sortMethod = $opt->sort;
                }
            }

            $this->query['select'] []= $selectItem;
        }

        if($aggFlag)
        {
            if($groupByExp === null)
            {
                RFAssert::Exception("Please specify a column to use for grouping.");
            }
            $this->query['group_by'] = array(
                $groupByExp
            );
        }

        if($setFirstColAsPrimaryFlag)
        {
            $this->dbCols["col_0"]['opt']->primaryColumn = true;
        }

        if($sortExp !== null)
        {
            if(!isset($this->query['order_by']))
            {
                $this->query['order_by'] = array();
            }

            $this->query['order_by'] []= array(
                'name' => $sortExp,
                'order' => $sortMethod
            );
        }

        if($this->paginateFlag)
        {
            // we need to determine the count of 
            // clone the query for the count
            $countSubQuery = $this->query;

            $countQuery = array();

            // no limits!!
            if(isset($countSubQuery['limit']))
                unset($countSubQuery['limit']);

            $countQuery['select'] = array(
                "COUNT(*) AS pagination_count"
            );

            $countQuery['from'] = array(
                array(
                    'subquery' => $countSubQuery,
                    'alias' => 'countSubQuery'
                )
            );

            $countRes = $this->RunQuery(DataSource::__objToQuery($countQuery));

            // TODO: Cache this for slower databases
            $count = $countRes[0]['pagination_count'];
            $this->total = $count;

            $this->debug("Found the pagination count as ", $count);

//            if($count <= $this->paginateSkip)
//            {
//                $pagSkip = $this->paginateSkip;
//                RFAssert::Exception("Error: The number of records to skip by is more than the number of records that exist $count and $pagSkip");
//            }

            $this->properties['count'] = $count;

            $this->limitTo($this->paginateCount, $this->paginateSkip);
        }

        if(count($this->customFilters))
        {
            $this->debug("Handling custom filters");
            // Since we're going to be running custom filters, we need to do this
            // at the top-level query
            // because the "SELECT" is not evaluated and the aliases won't be available.
            // we also can't use HAVING

            $innerQuery = $this->query;

            // ensure the limit applies to outer query. not the inner one
            $limit = $innerQuery['limit'];
            unset($innerQuery['limit']);

            $this->query = array(
                'select' => array(
                    '*'
                ),
                'from' => array(
                    array(
                        'type' => 'subquery',
                        'subquery' => $innerQuery,
                        'alias' => 'filter_subq'
                    )
                ),
                'where' => array(
                ),
                'limit' => $limit
            );

            foreach($this->customFilters as $filter)
            {
                $condition = "";
                if(count($filter) === 1)
                {
                    /** @var $filterExp DBExpression */
                    $filterExp = $filter[0];
                    $condition = $filterExp->toString();
                }
                else if(count($filter) === 3)
                {
                    /** @var $exp1 DBExpression */
                    $exp1 = $filter[0];
                    /** @var $exp2 DBExpression */
                    $exp2 = $filter[1];
                    $logic = $filter[2];

                    $condition = "(".$exp1->toString() . " $logic " . $exp2->toString() .")";
                }
                else {
                    RFAssert::Exception("Incorrect number of items in a customfilter");
                }

                // Add this as an AND where condition for the outermost extreme query
                $this->query['where'] []= array(
                    'cond' => $condition,
                    'bool' => "AND"
                );
            }
        }

        $rawData = $this->RunQuery(DataSource::__objToQuery($this->query));

        $finalData = array();

        foreach($rawData as $row) {
            $newRow = array();
            $hiddenData = array();
            foreach($this->dbCols as $key=>$col) {
                /** @var $colOpt TableColumnOptions */
                $colOpt = $col['opt'];

                if($colOpt->__hidden) {
                    // don't add hidden columns to the final data.
                    $hiddenData[$colOpt->__overrideID] = $row[$key];
                    continue;
                }
                if(!isset($col['exp'])) {
                    // custom columnn. ignore in data
                    continue;
                }

                $hiddenData[$key] = $row[$key];
                $newRow [$key] = $row[$key];
            }

            // Second pass to make it to all callbacks
            foreach($this->dbCols as $key => $col) {
                if(isset($col['callback'])) {
                    // Call the callback with the hidden Data
                    $newRow[$key] = $col['callback'] ($this, $hiddenData);
                }
            }

            $finalData []= $newRow;
        }

        $this->dataset['data'] = $finalData;

        if(count($this->customFilters) > 0) {
            // need to re-do the count.
            $this->query['select'] = array(
                array(
                    'func' => "COUNT",
                    'name' => '*',
                    'alias' => '_rf_newcount'
                )
            );

            // unset the limit for this query. As it will end up skipping
            // all the rows
            unset($this->query['limit']);

            $countData = $this->RunQuery(DataSource::__objToQuery($this->query));

            $this->total = $countData[0]['_rf_newcount'];
        }

        return false;
    }

    protected function cleanValue($value)
    {
        $result = $value;

        if(is_string($value))
        {
            $parsed = date_parse_from_format("D M d Y", $value);

            // We are expecting an error anyways, at point 15
            if(count($parsed['errors']) === 1) {
                if (isset($parsed['errors'][15]))
                {
                    $result = strftime("%F", strtotime($parsed["year"]."/".$parsed["month"]."/".$parsed["day"]));
                }
            }
            
        }
        return $result;
    }

    public function __handleRPC($rpcName, $params, $postBack = array())
    {
        $init = parent::__handleRPC($rpcName, $params, $postBack);
        if($init)
            return $init;

        if($rpcName === "ping")
        {
            RFLog::log("Got ping with params", $params);
            return "pong";
        }
        else if($rpcName === "getData")
        {
            $this->needsInit = false;
            $this->paginateFlag = true;

            $this->paginateSkip = RFRequest::get('skip',  0);
            $this->paginateCount = RFRequest::get('take', 0);

            // Add the sorting functionality
            if(RFRequest::check('sort'))
            {
                $sort = RFRequest::get('sort', array());

                if(count($sort) !== 1)
                {
                    RFAssert::Exception("Error. Multiple sorts not supported yet");
                }

                // unset any existing sort
                foreach($this->dbCols as $key => $val)
                {
                    $opt = $val['opt'];
                    /** @var $opt TableColumnOptions */
                    if($opt->sort !== "")
                    {
                        $opt->sort = "";
                        $this->dbCols [$key]['opt'] = $opt;
                    }
                }
                $key = $sort[0]['field'];
                $order = strtoupper($sort[0]['dir']);

                RFAssert::HasKey("The field should exist in columns", $this->dbCols, $key);

                // Manually override the dbCols' key
                $this->dbCols[$key]['opt']->sort = $order;
            }

            // Add the filtering functionality
            if(RFRequest::check('filter'))
            {
                $this->debug("Client has asked for filters");
                $filters = RFRequest::get('filter');

                if(isset($filters['filters']))
                {
                    $filters = $filters['filters'];
                    if(is_array($filters))
                    foreach($filters as $filter) {
                        if(isset($filter['filters']))
                        {
                            RFAssert::Internal("Multiple filters found. only single one is supported.");
                        }
                        else
                        {
                            RFAssert::AllKeysInArray("Array items need to be defined", array('field', 'operator', 'value'), $filter);

                            if($this->customFilterCheck($filter))
                                continue;

                            $this->debug("adding simple filter", $filter);
                            $filter['value'] = $this->cleanValue($filter['value']);

                            $this->customFilters []= array(
                                new DBExpression($filter['field'], $filter['operator'], $filter['value'], $this)
                            );
                        }
                    }
                }
            }

            $this->_initializeWrapper();
            // Actually run the data routines
            $this->dataInitialize();

            RFUtil::emitJSON(array(
                'data' =>$this->dataset['data'],
                'total' => $this->total
            ));
        }
        return false;
    }

    protected function customFilterCheck($item)
    {
        $col = $this->dbCols[$item['field']];

        /** @var $opt TableColumnOptions */
        $opt = $col['opt'];
        if($opt->displayAsRange === "timestamp")
        {
            $timeStamp = strftime("%Y-%m-%d", strtotime($item['value']));
            $this->addCondition($item['field'], $item['operator'], $timeStamp);
            return true;
        }
        return false;
    }


    protected function onActionTriggered($actionName, $params) {
        if(count($this->autoFilterTargets) > 0)
        {
            // Get the value of the primary column/cell clicked
            $value = $params['label'];

            // get the paimary column/cell click column
            $colId = $params['colId'];

            // Find the column expression
            $colExp = $this->dbCols["col_$colId"]['exp'];

            if(!isset($this->dbCols["col_$colId"])){
                RFAssert::Exception("Unable to find the column that was referenced from action");
            }

            if(isset($params['displayAsRange']))
            {
                $this->timeRangeType = $params['timeUnit'];
                $rangeCol = DBExpression::__consume($colExp, $this);
                $this->rangeColType = $params['displayAsRange'];

                $cond = new DBExpression($this->GetTimeFormatQuery($rangeCol->toString(), $this->timeRangeType), '=', $value, $this);

                /**
                 * @var $target Component
                 */
                foreach($this->autoFilterTargets as $target)
                {
                    $target->addSQLWhere($cond);
                }
            }
            else
            {
                $condOp = '='; // This is the only operation. TODO: Add more later

                $condition = new DBExpression("(".$colExp->toString().")", $condOp, $value, $this);
                $condition->__setlds($this);

                /** @var $target Component */
                foreach($this->autoFilterTargets as $target) {
                    $target->addSQLWhere($condition);
                }
            }

            return $this->autoFilterTargets;
        }

        return array();
    }


    /**
     * @docignore
     * @return string
     */
    public function getObjectType()
    {return __CLASS__;}
}

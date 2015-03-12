<?php

/**
 * The Component class contains common functionality which is available
 * for all components.
 *
 * Please see :ref:`components-intro` for an introduction on how to work with components.
 */
abstract class Component extends RFComponent {
    
    /**
     * Add a SQL WHERE clause to this component.
     *
     * .. note ::
     *
     *     1. The contents of the expression are not Escaped or binded to
     *        prepared queries. Using this function in a non-secure manner may open up
     *        your dashboard to SQL Injection vulnerabilities.
     *     2. This condition is only applied to this specific component, not to other
     *        components.
     *
     * @param string $exp The expression to add to the SQL Queries
     * @param string $boolOperand The boolean operand for the condition. ("AND"/"OR)
     */
    public function addSQLWhere($exp, $boolOperand = 'AND')
    {
        // add to cache invalidators
        $args = func_get_args();
        $this->invalidate($args);

        // Sanity checks
        $this->assertDataSourceLinked();
        RFAssert::InArray("Bool operand should be valid", $boolOperand, array("AND", "OR"));
        

        /** @var $child Component */
        foreach($this->__getChildComponents() as $child)
        {
            $child->addSQLWhere($exp, $boolOperand);
        }


        // Create a new DBExpresion
        $dbexp = DBExpression::__consume($exp, $this);

        $this->debug("Adding Where - ", $dbexp->toString());
        
        if(!isset($this->query['where']))
            $this->query['where'] = array();

        // Add this to the query tree
        $this->query['where'] []= array(
            'cond' => $dbexp->toString(),
            'bool' => $boolOperand
        );
    }
    
    /**
     * Sort the data in the component based on a particular expression. This is equivalent
     * to having an "ORDER BY" clause in your SQL Queries.
     *
     * However, this function might not work as expected in components which have data 
     * aggregated and grouped by another column. If this is the case, you should use the
     * in-built sorting functionality of the component itself.
     * 
     * .. note ::
     *
     *     1. The contents of the expression are not Escaped or binded to
     *        prepared queries. Using this function in a non-secure manner may open up
     *        your dashboard to SQL Injection vulnerabilities.
     *     2. This condition is only applied to this specific component, not to other
     *        components.
     * 
     * @param string $exp The expression to sort by
     * @param string $sortOrder The Sort order ("ASC"/"DESC")
     */
    public function sortOn ($exp, $sortOrder = "DESC")
    {
        // Invalidate cache
        $args = func_get_args();
        $this->invalidate($args);

        // Sanity Checks
        $this->assertDataSourceLinked();
        RFAssert::InArray("The sort order should be valid", $sortOrder, array("ASC", "DESC"));

        /** @var $child Component */
        foreach($this->__getChildComponents() as $child)
        {
            $child->sortOn($exp, $sortOrder);
        }


        $dbexp = DBExpression::__consume($exp, $this);
        
        $this->debug("Sorting - ", $dbexp->toString());

        if(!isset($this->query['order_by']))
        {
            $this->query['order_by'] = array();
        }
        
        $this->query['order_by'] []= array(
            'name' => $dbexp->toString(),
            'order' => $sortOrder
        );
    }
    
    /**
     * Limit the data in the component based on a particular expression. This is equivalent
     * to having an "LIMIT" clause in your SQL Queries.
     *
     * @param int $exp The number of records to take
     * @param int $sortOrder The number of records to skip
     */
    public function limitTo($take, $skip = 0)
    {
        // Invalidate
        $args = func_get_args();
        $this->invalidate($args);

        // Sanity checks
        $this->assertDataSourceLinked();
        // RFAssert::Assert("Limit should be numeric", is_numeric($take));
        // RFAssert::Assert("Skip should be numeric", is_numeric($skip));

        // Affect all child components
        /** @var $child Component */
        foreach($this->__getChildComponents() as $child)
        {
            $child->limitTo($take, $skip);
        }

        $this->debug("Applying LIMIT take: $take, skip: $skip");


        // MS SQL doesn't have a LIMIT clause. there is a better workaround, but until then,
        // We use an in-memory system to perform SKIP and take

        if(isset($this->query['limit']))
        {
            // Don't allow someone to set the limit more than once.
            RFAssert::Exception("limitTo can be set only once");
        }
        
        $this->query['limit'] = array(
            'skip' => $skip,
            'take' => $take
        );
    }

    /**
     * Add a condition to the component. This is similar to having a "WHERE" clause
     * in your SQL Query, but there are a few differences:
     *
     * 1. The ``$value`` parameter is protected against SQL Injection attacks
     *    so you do not need to worry about escaping the value.
     * 2. There are specific comparisons that you can perform
     *
     * The comparison operators you can use are:
     *
     * * Equality: "eq", "==", "isequalto", "equals", "equalto", "equal"
     * * Inequality: "neq", "!=", "isnotequalto", "notequals", "notequalto", "notequal", "ne"
     * * Less than:  "lt", "<", "islessthan", "lessthan", "less"
     * * Less than or equal to: "lte", "<=", "islessthanorequalto", "lessthanequal", "le"
     * * Greater Than: "gt", ">", "isgreaterthan", "greaterthan", "greater"
     * * Greater Than or Equal To: "gte", ">=", "isgreaterthanorequalto", "greaterthanequal", "ge"
     * * Starts with: "startswith"
     * * Ends wit: "endswith"
     * * Contains: "contains"
     *
     * @param string $expression The column or expression to filter
     * @param string $comparison The comparison operator
     * @param mixed $value The value to compare against
     * @param string $boolOperand The boolean operand (AND/OR)
     */
    public function addCondition($expression, $comparison, $value, $boolOperand = "AND")
    {
        // Convert the exp into a DBExpression and make that a string. This will ensure
        // that we can use either a DBExpression or a string and it'll work regardless
        /** @var $child Component */
        foreach($this->__getChildComponents() as $child)
        {
            $child->addCondition($expression, $comparison, $value, $boolOperand);
        }

        $exp = DBExpression::__consume($expression, $this);

        // Create a complex DBExpression for the condition and add it to where.
        $this->addSQLWhere(new DBExpression($exp->asString(), $comparison, $value), $boolOperand);
    }

    /**
     * Add a condition which is only applied if there isn't a drilldown.
     *
     * Identical behavior to :php:meth:`Component::addCondition`
     * 
     * @param string $expression The column or expression to filter
     * @param string $comparison The comparison operator
     * @param mixed $value The value to compare against
     * @param string $boolOperand The boolean operand (AND/OR)
     */
    public function addInitialCondition ($expression, $comparison, $value, $boolOperand = "AND")
    {
        // Check if there's a drilldown active.
        
        $postback = RFRequest::getPostback();
        if(isset($postback['actions'])) {
            // If *any* postback exists, don't bother applying any actions.
            $count = count(array_keys($postback['actions']));

            if($count === 0)
            {
                $this->addCondition($expression, $comparison, $value, $boolOperand);
            }
        }
        return;
    }
    
    /**
     * Set the DataSource for this component.
     *
     * The DataSource contains information on where to access the data
     * to display in the component.
     * 
     * @param DataSource $datasource The datasource
     */
    public function setDataSource ($datasource)
    {
        $this->dataSource = $datasource;
        $this->query = $this->dataSource->__getPristineQuery();

        $this->debug("Attaching a datasource");


        /** @var $child Component */
        foreach($this->__getChildComponents() as $child)
        {
            $child->setDataSource($datasource);
        }

        // Register this component as a linked component
//        $datasource->__registerLinkedComponent($this);
        $this->query = $this->dataSource->__getPristineQuery();
    }
    
    
    /**
     * This function is **Not Supported** currently
     * 
     * @notsupported
     * @internal
     */
    public function setCaching ($option = false)
    {
        /** @var $child Component */
        foreach($this->__getChildComponents() as $child)
        {
            $child->setCaching($option);
        }

        $this->cachingEnabled = $option;
    }

    /**
     * After processing data (querying, fetching, filtering, etc),
     * you can register a callback to be called with each row. Note
     * that the callback can modify any row and return the final row.
     *
     * Let's say that you have a pie chart, but your database has values
     * 0 and 1. You want to rewrite labels 0 => "Not Available" and 1 => "Available"
     * you can add a callback like this::
     *
     *     $pieChart->addPostProcessCallback('rewrite_labels');
     *
     *     function rewrite_labels ($row, $sourceComponent) {
     *         if($row['rfLabels'] === '0') {
     *             $row['rfLabels'] = "Not Available";
     *         }
     *         else if($row['rfLabels'] === '1') {
     *             $row['rfLabels'] = "Available";
     *         }
     *         return $row; // Don't forget this
     *     }
     *
     * @param  function $callback A function or name of function to execute
     */
    public function addPostProcessRowCallback($callback) {
        $this->postProcessRowCallbacks []= $callback;
    }

    /**
     * Get the dataset (for testing purposes)
     *
     * @internal
     * @return array
     */
    public function __getDataSet() {
        return $this->dataset;
    }

    protected $dataset = array('columnOptions' => array(), 'data' => array());


    /**
     * The central Query descriptor.
     * @internal
     * @var array
     * @ignore
     */
    public $query;


    /**
     * @var DataSource
     */
    protected $dataSource;

    /**
     * Is caching enabled? yes/no
     * @var boolean
     */
    protected $cachingEnabled = true;

    /**
     * A string that the component will append text to
     *
     * @var string
     */
    protected $cacheInvalidators = array();

    /**
     * Calculated cache key from the cacheInvalidators
     * @var string
     */
    protected $cacheKey = "";

    /**
     * The cached value of the component. this is an array with two keys:
     * dataset and properties, which go and apply to the dataset and properties respectively
     *
     * @var null
     */
    protected $cachedValue = null;

    protected $selectItems = array();

    protected $rangeCol, $rangeColType, $timeRangeType, $numRangeValueSplits = 5;

    /**
     * Specify a maximum number of cases to consider
     * @var int
     */
    protected $rangeLimitNumberOfCases;

    /**
     * If this is set, RF will do an in-memory skip over the result set
     * this is to support databases which don't support "skip and take"
     *
     * @var int
     */
    protected $inMemorySkip;

    /**
     * If this is set, RF will do an in-memory take over the result set
     * this is to support databases which don't support "skip and take"
     *
     * @var int
     */
    protected $inMemoryTake;

    // A utility variable, which can be used to generate a WHERE clause from
    // the ranged CASEs.
    protected $_rangeCaseList;

    // A js format string to pass to timestamps
    protected $jsFormatString;

    protected $postProcessRowCallbacks = array();


    /**
     *
     */
    protected function initialize()
    {
//        // ask the datasource to provide all the changed conditions
//        if(isset($this->_ds))
//            $this->_ds->__augumentComponent($this);

        $init = parent::initialize();
        if($init)
            return $init;

        if(isset($this->dataSource)) {
            // $this->dataSource->initialize();
            
            if(!$this->dataSource->__isDataSourceReady()) {
                $this->properties['componentMessage'] = $this->dataSource->__getDataSourceNotReadyMessage();
                $this->properties['componentNotReady'] = true;

                // Stop actual component initialization
                return true;
            }
        }

        return false;
    }

    protected function assertDataSourceLinked ()
    {
        if(!isset($this->dataSource))
            RFAssert::Exception("Please link a data source to the component before calling this function");
    }
    protected function _assertDataSourceNotLinked ()
    {
        if(isset($this->dataSource))
            RFAssert::Exception("You cannot use array functionality if a datasource is linked to the component.");
    }

    protected $drillParents = array();

    public function __registerDrillParent($source)
    {
        $this->drillParents []= $source;
    }

    protected function postInitialize()
    {
        parent::postInitialize();
        if(count($this->postProcessRowCallbacks) > 0)
        {
            $newData = array();
            foreach($this->dataset['data'] as $row) {
                $newRow = $row;
                foreach($this->postProcessRowCallbacks as $callback) {
                    $newRow = $callback($newRow, $this);
                }
                $newData []= $newRow;
            }
            $this->dataset['data'] = $newData;
        }
        return false;
    }



    /**
     * Add an additional item that will invalidate the cache
     *
     * @param array $list A bunch of invalidators
     */
    protected function invalidate($list)
    {
        $invalidators = array();

        foreach($list as $item)
        {
            if(is_object($item))
            {
                if(method_exists($item, 'toString'))
                {
                    // Don't try to serialize the entire DBExpression
//                    $invalidators []= $item->toString();
                    continue;
                }
            }
            $invalidators []= $item;
        }

        $this->cacheInvalidators []= $invalidators;
    }



    
    public function __construct()
    {
        parent::__construct();
    }

    protected function GetNumberFormatter($rangeType)
    {
        $formatString = "";

        // This is what gets passed to the clientside to format
        // numbers right before display
        switch($rangeType)
        {
            case 'year':
                $formatString = "yyyy";
            break;
            case 'month':
                $formatString = "Y";
            break;
            case 'day':
                $formatString = "dd, ddd";
            break;
            case 'hour':
                $formatString = "hh tt";
            break;
            case 'minute':
                $formatString = "mm 'mins'";
            break;
            case 'second':
                $formatString = "ss";
            break;
        }

        return $formatString;
    }

    protected function GetTimeFormatQuery ($expr, $type) {

        return $this->dataSource->__getTimeFormatQuery($expr, $type);
    }

    protected function InPlaceRange () {
        $this->log("Doing an in place range");

        RFAssert::Assert("The range column must be set", isset($this->rangeCol));
        RFAssert::Assert("The range columntype must be set", isset($this->rangeCol));

        // A target exprssion
        $target = DBExpression::__consume($this->rangeCol, $this);

        // The result expression to hold the query
        $resultExp = array();
        $sourceExp = $target->toString();
        $sourceAlias = $target->getAlias();

        if($this->rangeColType === 'time')
        {
            $resultExp = array(
                'name' => $this->GetTimeFormatQuery($sourceExp, $this->timeRangeType),
                'alias' => $sourceAlias
            );
        }

        return $resultExp;
    }

    protected function RunQuery ($queryString) {
        $this->log("Running a Query: $queryString");
        $this->log("Binding: ", $this->binded);
        $startTime = microtime();
        $data = $this->dataSource->__query($queryString, $this->binded);
        // $this->log("The data is ", $data);
        $count = count($data);
        $totalTime = microtime() - $startTime;
        $this->log("Query finished with $count rows and took $totalTime seconds");

        if(RFConfig::isSetAndTrue('utf8_recode')) {
            // TODO
        }

        if(RFConfig::isSetAndTrue('debug')){
            $firstTwoRows = array();
            $keys = array();
            if(isset($data[0]))
            {
                $firstTwoRows = array_slice($data, 0, 2);
                $keys = array_keys($data[0]);
            }

            RFDevTools::RegisterQuery(array(
                'queryString' => $queryString,
                'params' => $this->binded,
                'id' => $this->getID(),
                'caption' => $this->caption,
                'time' => $totalTime,
                'data' => $firstTwoRows,
                'count' => $count,
                'keys' => $keys
            ));
        }

        return $data;
    }

    protected $binded = array();
    protected function bind($key, $value)
    {
        $type = null;

        if(is_string($value)) {
            $type = PDO::PARAM_STR;
        }
        else if(is_numeric($value)) {
            $type = PDO::PARAM_INT;
        }
        else {
            RFAssert::Exception("Unknown type of $value to bind");
        }

        $this->binded[$key] = array(
            'val'=>$value,
            'type' => $type
        );
    }

    protected function randbind ($value) {
        // We need to create a random string. Let's cheat and use a sha1 of a random number
        $key = substr(preg_replace('/[0-9]/', '', sha1(''.rand(0,1000))), 0, 6);

        $this->bind($key, $value);

        return $key;
    }


    /**
     * @internal
     * @param  array $values the values to bind
     */
    public function __arraybind ($values) {
        $this->binded = array_merge($this->binded, $values);
    }

    /**
     * @internal
     * Create a bind and return the random string
     * 
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function __randbind ($value) {
        return $this->randbind($value);
    }
}

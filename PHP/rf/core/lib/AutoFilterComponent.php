<?php

class AutoFilterComponent extends RFFilterComponentCore {
    /**
     * Add a numeric range filter.
     *
     * A numeric range filter is useful when you want your users to see only
     * data with a particular expression evaluating to a value which is
     * between two numbers specified in the filter form.
     *
     * @param string $caption
     * @param DBExpression $expression
     * @param FilterItemOptions|array $options
     */
    public function addNumericRangeFilter ($caption, $expression, $options = array())
    {
        $this->_addFilter('numeric', $caption, $expression, $options);
    }

    /**
     * Add a time range filter
     * 
     * A time range filter is useful when you want your users to see only
     * data with a particular expression evaluating to a timestamp value is
     * between two dates specified on the filter form.
     *
     * @param string $caption
     * @param DBExpression $expression
     * @param FilterItemOptions|array $options
     */
    public function addTimeRangeFilter ($caption, $expression, $options = array())
    {
        $this->_addFilter('time', $caption, $expression, $options);
    }


    /**
     * Add a text filter.
     * 
     * A text filter is useful when you want your users to see only
     * data with a particular expression evaluating to a text value is
     * equal to the value specified by the user.
     *
     * @param string $caption
     * @param DBExpression $expression
     * @param FilterItemOptions|array $options
     */
    public function addTextFilter ($caption, $expression, $options = array())
    {
        $this->_addFilter('text', $caption, $expression, $options);
    }


    /**
     * Add a multi select filter.
     *
     * Your users are given a list of options to choose one or more from, and records matching
     * the items they have selected are displayed.
     * TODO: improve the docs.
     * 
     * @param string $caption
     * @param DBExpression $expression
     * @param FilterItemOptions|array $options
     */
    public function addMultiSelectFilter ($caption, $expression, $options = array())
    {
        $this->_addFilter('multiselect', $caption, $expression, $options);
    }

    /**
     * Add a multi select filter.
     *
     * Your users are given a list of options to choose a single one from, and records matching
     * the items they have selected are displayed.
     * 
     * @param string $caption
     * @param DBExpression $expression
     * @param FilterItemOptions|array $options
     */
    public function addSelectFilter ($caption, $expression, $options = array())
    {
        $this->_addFilter('select', $caption, $expression, $options);
    }

    /**
     * Filter the datasource that is linked to this component. Thus, every component linked to
     * that datasource is also filtered.
     *
     * @param array $filterOptions
     */
    public function filterLinkedDataSource($filterOptions = array())
    {
        $dsLinkedComponents = $this->_ds->__getLinkedComponents();
        foreach($dsLinkedComponents as $component)
        {
            $this->addFilterTo($component, $filterOptions);
        }
    }

    /**
     * Apply the user filters on a component. Note that the component being filtered to has to be
     * linked to the same datasource as this filter.
     *
     * @param $component Component
     * @param array $filterOptions
     */
    public function addFilterTo($component, $filterOptions = array())
    {
        $this->filteredComponents []= $component;
    }

    /**
     * All the filters of the component
     *
     * @var array
     */
    protected $filters = array();

    /**
     * @var array
     */
     protected $filteredComponents = array();
    
    /**
    * The number of filters in this component
    * 
    * @var int
    */
    protected $nFilters = 0;


    protected function _addFilter($type, $caption, $exp, $options)
    {
        $args = func_get_args();
        $this->invalidate($args);
        $exp = DBExpression::__consume($exp, $this);
        $opt = new FilterItemOptions($options);

        $index = $this->nFilters;

        $this->filters ["filter_$index"]= array(
            'type' => $type,
            'caption' => $caption,
            'exp' => $exp,
            'opt' => $opt
        );
        $this->nFilters ++;
    }



    public function initialize() {
        $init = parent::initialize();
        if ($init)
            return $init;

        $index = 0;
        foreach($this->filters as $filter)
        {
        	$filterKey = "filter_$index";
            /*
             * Four options for type:
             *
             * text - no searching for existing values
             * select - find multiple distinct values
             * numeric - find max/min
             * time - find max/min
             */
            $filterType = $filter['type'];

            /** @var $exp DBExpression */
            $exp = $filter['exp'];

            // Create a copy of the master query to run extremity checks
            $valQuery = $this->query;

            // remove any limits
            unset($valQuery['limit']);

            // remove any items for select if it has been set
            unset($valQuery['select']);

            if($filterType === "select" || $filterType === "multiselect")
            {
                // SELECT DISTINCT(exp) as value FROM Table;

                $valQuery ['select'] = array(
                    array(
                        'type' => 'func',
                        'func' => 'DISTINCT',
                        'name' => $exp->toString(),
                        'alias' => 'value'
                    )
                );

                // create an array to hold all distinct values
                $distinct = array();

                // run the query to get all the distinct values
                $distinctVals = $this->RunQuery(DataSource::__objToQuery($valQuery));

                foreach($distinctVals as $row)
                {
                    $distinct []= $row['value'];
                }

                // now, call the parent method's 

                // $constraint['values'] = $distinct;
                if($filterType === "select")
                {
                	$this->addSelectItem($filterKey, $filter['caption'], $distinct, 0);
                }
                else if($filterType === "multiselect")
                {
                	$this->addMultiSelectItem ($filterKey, $filter['caption'], $distinct, array());
                }
                
            }
            else if($filterType === "numeric" || $filterType === "time")
            {
                // SELECT MAX(exp) AS max_val, MIN(exp) AS min_val FROM Table;

                $valQuery ['select'] = array(
                    array(
                        'type' => 'func',
                        'func' => 'MAX',
                        'name' => $exp->toString(),
                        'alias' => 'max_val'
                        ),
                    array(
                        'type' => 'func',
                        'func' => 'MIN',
                        'name' => $exp->toString(),
                        'alias' => 'min_val'
                        )
                );

                $minmax = $this->RunQuery(DataSource::__objToQuery($valQuery));

                // $constraint['values'] = array($minmax[0]['min_val'], $minmax[0]['max_val']);
                if($filterType === "numeric")
                {
                	$this->addNumericRangeItem($filterKey, $filter['caption'], array($minmax[0]['min_val'], $minmax[0]['max_val']));
                }
                else if($filterType === "time")
                {
                    // for some reason, JS's Date() cannot pick up strings from SQLite

                    $timeExtremities = array(
                        date("m/d/Y", strtotime($minmax[0]['min_val'])),
                        date("m/d/Y", strtotime($minmax[0]['max_val']))
                    );

                    $this->addTimeRangeItem($filterKey, $filter['caption'], $timeExtremities);
                }
            }
            else if($filterType === "text")
            {
            	$this->addTextItem($filterKey, $filter['caption']);
            }


            $index ++;
        }
        $this->addItemsToProperties();
        return false;
    }

    public function onActionTriggered($actionName, $params)
    {
        if($actionName === "filterApply")
        {
            // get a list of all array keys from the action prams
            $keys = array_keys($params);

            foreach($keys as $filterKey)
            {
                $filter = $this->filters[$filterKey];

                $expString = $filter['exp']->toString();

                $values = $params[$filterKey];

                foreach($this->filteredComponents as $comp)
                {
                    switch($filter['type'])
                    {
                        case 'text':
                        case 'select':
                            if(strlen($values) > 1)
                                $comp->addCondition($expString, '=', $values);
                        break;
                        case 'multiselect':
                            $inList = array();

                            foreach($values as $val)
                            {
                                $inList []= ":".$comp->randbind($val);
                            }
                            $inList = implode(",", $inList);
                            if(count($values) > 0)
                                $comp->addSQLWhere("$expString IN ($inList)");
                        break;
                        case 'time':
                        case 'numeric':


                            if($filter['type'] === 'time')
                            {
	                            $minVal = strtotime($values[0]);
	                            $maxVal = strtotime($values[1]);

                                $minVal = ':'.$comp->randbind(strftime("%Y-%m-%d", $minVal));
                                $maxVal = ':'.$comp->randbind(strftime("%Y-%m-%d", $maxVal));
                                $comp->addSQLWhere("$expString BETWEEN $minVal AND $maxVal");
                            }
                            else if ($filter['type'] === "numeric")
                            {
                            	$minVal = ":".$comp->randbind(floatval($values[0]));
                            	$maxVal = ":".$comp->randbind(floatval($values[1]));
                            	$comp->addSQLWhere("($expString > $minVal AND $expString < $maxVal)");
                            }
                        break;
                        default:
                            RFAssert::Exception("Unknown filter type");
                    }
                }
            }
            return $this->filteredComponents;
        }

        return array();
    }
}
<?php


/**
 *
 */
class DBExpression
{
    /**
     * The raw string of the Database expression
     * @var mixed
     */
    protected $rawString;


    /**
     * @ignore
     * @var string
     */
    public $alias;

    protected $lhs, $op, $rhs;

    protected $bindKey;

    /**
     * @ignore
     */
    public function __construct()
    {
        $numArgs = func_num_args();

        if($numArgs === 0)
        {
            RFAssert::Exception("Cannot create a DBExpression without any arguments");
        }
        else if($numArgs === 1)
        {
            // This has to be a raw SQL string
            $this->rawString = func_get_arg(0);

            if(!is_string($this->rawString))
            {
                RFAssert::Exception("DBExpressions must be created with strings");
            }

            return;
        }
        else if($numArgs === 3)
        {
            $this->lhs = func_get_arg(0);
            $this->op = func_get_arg(1);
            $this->rhs = func_get_arg(2);

            $this->processOps();

            return;
        }
        else if($numArgs === 4)
        {
            $this->lhs = func_get_arg(0);
            $this->op = func_get_arg(1);
            $this->rhs = func_get_arg(2);

            $linkedComponent = func_get_arg(3);
            $this->__setlds($linkedComponent);

            $this->processOps();

            return;
        }

        RFAssert::Exception("The arguments to the DBExpression are invlaid");
    }


    protected function processOps() {
        // Do a little smoothening out for the ops Support everything kendo supports
        /*
        Equal To
        "eq", "==", "isequalto", "equals", "equalto", "equal"
        Not Equal To
        "neq", "!=", "isnotequalto", "notequals", "notequalto", "notequal", "ne"
        Less Then
        "lt", "<", "islessthan", "lessthan", "less"
        Less Then or Equal To
        "lte", "<=", "islessthanorequalto", "lessthanequal", "le"
        Greater Then
        "gt", ">", "isgreaterthan", "greaterthan", "greater"
        Greater Then or Equal To
        "gte", ">=", "isgreaterthanorequalto", "greaterthanequal", "ge"
        Starts With
        "startswith"
        Ends With
        "endswith"
        Contains
        "contains"
         */
        
        $opMap = array(
            '=' => array('=', 'eq', '==', 'isequalto', 'equalto', 'equal'),
            '<>' => array('<>', 'neq', '!=', 'isnotequalto', 'notequals', 'notequalto', 'notequal', 'ne'),
            '<' => array('<', 'lt', 'islessthan', 'lessthan', 'less'),
            '<=' => array("lte", "<=", "islessthanorequalto", "lessthanequal", "le"),
            '>' => array("gt", ">", "isgreaterthan", "greaterthan", "greater"),
            '>=' => array("gte", ">=", "isgreaterthanorequalto", "greaterthanequal", "ge"),
            'LIKE' => array('startswith', 'endswith', 'contains'),
            'FOO' => array('does', 'not', 'exist')
        );

        $realOpMap = array();
        foreach($opMap as $op => $list)
        {
            foreach($list as $fakeOp)
            {
                $realOpMap[$fakeOp] = $op;
            }
        }

        // add a startswith/endswith op handling
        if(is_string($this->rhs))
        {
            switch($this->op)
            {
                case 'startswith':
                    $this->rhs = $this->rhs."%";
                    break;
                case 'endswith':
                    $this->rhs = "%".$this->rhs;
                    break;
                case 'contains':
                    $this->rhs = "%".$this->rhs."%";
                    break;
                default:
                    break;
            }
        }

        if(isset($this->linkedComponent)) {
            $this->bindKey = $this->linkedComponent->__randbind($this->rhs);
        }

        RFAssert::HasKey("The operator needs to be valid", $this->op, $realOpMap);

        $this->op = $realOpMap[$this->op];
    }

    /**
     * @ignore
     * @return mixed|string
     */
    public function toString()
    {
        $coreExp = "";
        if(isset($this->rawString))
            $coreExp = $this->rawString;

        else if(isset($this->lhs))
        {
            if(!isset($this->bindKey))
            {
                RFAssert::Exception("DBExpression hasn't been binded with the component");
            }
            return $this->lhs . " " . $this->op . " :" . $this->bindKey;
        }
        else{
            RFAssert::Internal("Unknown DBExpression Type ");
        }

        $finalExp = $coreExp;

        return $finalExp;
    }

    public function asString()
    {
        return $this->toString();
    }

    /**
     * @ignore
     * @return string
     */
    public function getAlias ()
    {
        // if an alias is already set, then return it.
        if(isset($this->alias))
            return $this->alias;

        $expString = $this->toString();

        // otherwise, return the sha1 of the expression string
//        return RFUtil::dbFriendlyHash($expString);
    }

    /**
     * @var Component
     */
    protected $linkedComponent;

    /**
     * @var DataSource
     */
    //protected $dataSource;

    /**
     * Internal function. do not use.
     *
     * @param Component $l
     */
    public function __setlds($l)
    {
        $this->linkedComponent = $l;

        if(isset($this->rhs)) {
            $this->bindKey = $this->linkedComponent->__randbind($this->rhs);
        }
    }

    /**
     *
     * Consumes a DBExpression as a string or object and always returns an object
     *
     * A small utility method for
     *
     * @static
     * @param $obj string|DBExpression
     * @param $linkedComponent Component
     * @return \DBExpression
     */
    public static function __consume($obj, $linkedComponent)
    {
        /** @var $exp DBExpression */
        $exp = null;
        if($obj instanceof DBExpression)
        {
            $exp = $obj;
        }
        else if (is_string($obj))
        {
            $exp = new DBExpression($obj);
        }
        else
        {
        }

        $exp->__setlds($linkedComponent);

        return $exp;
    }
}

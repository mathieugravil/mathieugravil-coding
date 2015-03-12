<?php

class DataSource {
    /**
     * The PDO Connection
     * @var PDO
     */
    protected $db;

    public function __getTimeFormatQuery ($expr, $type)
    {
        return "";
    }

    public function __ifnullexp($expr, $default)
    {
        return "";
    }

    public function __query($sql, $bind = array()) {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sth = $this->db->prepare($sql);


        foreach ($bind as $key => $value) {
            $type = $value['type'];
            $val = $value['val'];

            if(stripos($sql, $key))
            {
                // Fix a bug where the bind value is set even for 
                $sth->bindValue(":$key", $val, $type);
            }

        }

            $sth->execute();
        }
        catch (Exception $ex)
        {
            $errorMsg = $ex->getMessage();
            $msg = "There was an error in the generated SQL Query. The error is:\n$errorMsg\n\nThe Query is: \n'$sql'\n\nThe values binded are:\n".print_r($bind, true);

            throw new Exception($msg);
        }

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function __getPristineQuery () {
        return array(
            'from' => array($this->sourceText)
        );
    }

    public function __handleRefresh () {

    }

    /**
     * Set the SQL source from which the data is seeded.        
     * @param string $source 
     */
    public function setSQLSource ($source) {
        $this->sourceText = $source;

        if(RFConfig::isSetAndTrue('debug')) {
            $tempQuery = array(
                'select' => array('*'),
                'from' => array(
                    $this->sourceText
                ),
                'limit' => array(
                    'take' => 2,
                    'skip' => 0
                )
            );

            $queryText = self::__objToQuery($tempQuery);
            RFLog::log("Going to run the datasource sample query: ", $queryText);

            $startTime = microtime();
            $data = $this->__query($queryText, array());
            $count = count($data);
            $totalTime = microtime() - $startTime;

            $keys = array();

            if(isset($data[0]))
            {
                $keys = array_keys($data[0]);
            }

            $firstTwoRows = $data;


            RFDevTools::RegisterDataSource(array(
                'queryString' => $queryText,
                'params' => array(),
                'id' => "DataSource_".rand(0, 255),
                'caption' => $this->getName(),
                'time' => $totalTime,
                'data' => $firstTwoRows,
                'count' => $count,
                'keys' => $keys
            ));
        }
    }
    protected $sourceText = '';

    protected static function implodeItems ($prefix, $items)
    {
		if(count($items) > 0)
			return "$prefix \n\t".implode(",\n\t", $items)."\n";
		return "";
	}

    protected function getName () {
        return "DataSource";
    }

    public static function __objToQuery ($obj, $subq = false) {
        $out = "";

        if(isset($obj['select']))
        {
            $items = array();
            foreach($obj['select'] as $item)
            {
                if (is_string($item)) {
                    $items []= $item;
                }
                if (is_array($item)) {
                    $msg = "";
                    if(isset($item['name'])) {
                        $msg = $item['name'];
                    }

                    if(isset($item['func']))
                    {
                        $msg = $item['func'].'('.$msg.')';
                    }

                    if(isset($item['alias'])) {
                        $msg = "$msg AS ".$item['alias'];
                    }

                    $items []= $msg;
                }
            }
			$out .= self::implodeItems("SELECT", $items);
		}
        if(isset($obj['from']))
        {
            $items = array();
            foreach($obj['from'] as $item)
            {
                if (is_string($item)) {
                    $items []= $item;
                }
                else if (is_array($item)) {
                    $msg = "";
                    if(isset($item['name'])) {
                        $msg = $item['name'];
                    }

                    if(isset($item['subquery'])) {
                        $msg = self::__objToQuery($item['subquery'], true);

                        // add an additional tab level for a subquery
                        $msg = str_replace("\n", "\n\t", $msg);
                    }

                    if(isset($item['alias'])) {
                        $msg = "($msg) AS ".$item['alias'];
                    }

                    $items []= $msg;
                }
            }
            $out .= self::implodeItems("FROM", $items);
        }
        if(isset($obj['where']))
        {
            $index = 0;
			$out .= "WHERE \n";
			foreach($obj['where'] as $item)
            {
                if (is_array($item)) {
                    $msg = "";
                    if(isset($item['cond'])) {
                        $msg = "\t(".$item['cond'].")";
                    }

                    $bool = "AND";
                    if(isset($item['operand']))
                    {
                        $bool = $item['operand'];
                    }

                    if($index < count($obj['where']) - 1)
                    {
                        $msg .= " $bool";
                    }

                    $out .= $msg."\n";
                }
                $index ++;
            }
		}

        if(isset($obj['group_by']))
        {
            $out .= self::implodeItems("GROUP BY", $obj['group_by']);
        }

        if(isset($obj['order_by']))
        {
            $items = array();
            foreach($obj['order_by'] as $key => $value) {
                $msg = $value['name']." ".$value['order'];
                $items []= $msg;
            }

            $out .= self::implodeItems("ORDER BY", $items);
        }

        if(isset($obj['limit']))
        {
            $val = $obj['limit'];

            $out .= "LIMIT ";

            if(!isset($val['take']) && isset($val['skip']))
            {
                RFAssert::Exception("Cannot skip N and take none");
            }
            else if(isset($val['take']) && !isset($val['skip']))
            {
                $out .= intval($val['take']);
            }
            else if (isset($val['take']) && isset($val['skip'])) {
                $out .= intval($val['skip']).','.intval($val['take']);
            }
            else {
                RFAssert::Exception("Neither skip or take is defined in the limit");
            }
        }

        if(!$subq)
        {
            $out .= ";";
        }

        return $out;
    }

    public function __getDataSourceNotReadyMessage () {
        return "The data is currently not available. Please contact the dashboard administrator";
    }


    protected $readyFlag = false;
    public function __isDataSourceReady() {
        return $this->readyFlag;
    }

    public function __setDataSourceIsReady () {
        $this->readyFlag = true;
    }

    public function __construct () {
        Dashboard::__registerDataSource($this);
    }
}

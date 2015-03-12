<?php

abstract class RFCachedDataSource extends SQLiteDataSource {

    protected $inMemory = true;
    protected $cacheFileName;
    protected $cacheExists = false;
    protected $cacheOverwrite = false;
    protected $cacheDuration = 600;

    /**
     * Loads data into the internal database. This is an internal method.
     */
    public function initialize () {
        $this->cdsRefreshed = true;
        if(!isset($this->db)) {
            $this->__setDataSourceIsReady();
            if($this->inMemory)
            {
                $this->db = new PDO('sqlite::memory:');
                $this->createTable();
                $this->loadDataIntoTable();
            }
            else {
                $cacheExists = $this->cacheExists();
                $cacheFileName = $this->cacheFileName;

                if($cacheExists) {

                    $this->db = new PDO("sqlite:$cacheFileName");
                    $this->setSQLSource('data');
                }
                else {
                    $this->db = new PDO("sqlite:$cacheFileName");
                    $this->createTable();
                    $this->loadDataIntoTable();
                }
            }

        }
    }

    protected $cdsRefreshed = false;
    public function __handleRefresh() {
        if(!$this->cdsRefreshed) {
            $this->initialize();
        }
    }

    /**
     * Even if a cache is available, overwrite it.,
     */
    public function forceCacheOverWrite () {
        $this->cacheOverwrite = true;
    }

    /**
     * Configure the caching, by passing an array with the following keys (both optional)
     *
     * * ``enabled`` => true/false - whether caching is enabled
     * * ``cacheDuration`` => number of seconds before data is refreshed
     *
     * @param array $caching Array containing caching parameters
     */
    public function configureCaching($caching = array()) {
        if(isset($caching['enabled'])) {
            $this->inMemory = !$caching;
        }
        if(isset($caching['cacheDuration'])) {
            $this->cacheDuration = $caching['cacheDuration'];
        }
    }

    /**
     * Enables caching
     */
    public function enableCaching() {
        $this->configureCaching(array('enabled' => true));
    }
        
    

    /**
     * Get a unique string based on the current invalidators
     *
     * @return string
     */
    protected function getCacheKey () {
        $cacheKey = "";
        $cacheKey .= json_encode($this->getSchema());
        $cacheKey .= json_encode($this->invalidators);

        $cacheKey = sha1($cacheKey);
        return $cacheKey;
    }

    /**
     * Check if a cache exists for the current datasource
     *
     * @return bool true if cache exists
     */
    public function cacheExists () {
        $cacheKey = $this->getCacheKey();


        $dbFolder = RFUtil::getTempDir().DIRECTORY_SEPARATOR.substr(sha1(__FILE__), 0, 5).DIRECTORY_SEPARATOR;

        if(!is_dir($dbFolder)) {
            mkdir($dbFolder);
        }

        $dbFiles = scandir($dbFolder);

        // Perform housekeeping
        foreach($dbFiles as $item) {
            if($item === "." || $item === "..") {
                continue;
            }
            $pos = strpos($item, "_");
            if($pos !== false) {
                // Get the time to live of the cache file in seconds
                $timeToLive = intval(substr($item, $pos + 1));
                $fullPath = $dbFolder.DIRECTORY_SEPARATOR.$item;
                if(time() - filemtime($fullPath) > $timeToLive) {
                    RFLog::log("Removing file $item");
                    unlink($fullPath);
                }
            }
        }

        $cacheFileName = $dbFolder.$cacheKey.'_'.$this->cacheDuration.'.db';
        $this->cacheFileName = $cacheFileName;

        RFLog::log("Calculated the cache file name as ", $this->cacheFileName);

        $fileExistsFlag = file_exists($cacheFileName);

        if($fileExistsFlag && $this->cacheOverwrite) {
            // remove the cache file so it can be re-created
            unlink($this->cacheFileName);
            $fileExistsFlag = false;
        }

        if($fileExistsFlag === false)
        {
            RFLog::log("Could not find a cached file");
            return false;
        }

        RFLog::log("found a cache file");
        $this->cacheExists = true;
        return true;
    }

    protected $invalidators = array();
    protected function addInvalidator ($item) {
        $this->invalidators[]= $item;
    }

    public function __construct() {

    }

    protected function createTable () {
        $schema = $this->getSchema();

        $query = "CREATE TABLE data (\n";

        $queryParts = array();
        foreach($schema as $key => $value)
        {
            $type = "";
            if($value['type'] === "number")
                $type = "REAL";
            else if ($value['type'] === "text")
                $type = "TEXT";
            else if ($value['type'] === "datetime")
                $type = "DATETIME";
            else
            {
                $typeName = $value['type'];
                RFAssert::Exception("Unknown type $typeName in the schema");
            }

            $queryParts []= "$key $type";
        }
        $query .= implode(",\n", $queryParts);
        $query.= "\n);";
        $this->__query($query);

        $metaQuery = <<<MQ
CREATE TABLE meta (
key TEXT,
value TEXT
)
MQ;
        $this->__query($metaQuery);
    }


    protected function createMeta ($key, $value)
    {
        $this->__query("INSERT INTO meta VALUES(:key, :value)", array(
            'key'=>array(
                'val' => $key,
                'type' => PDO::PARAM_STR),
            'value'=> array(
                'val' => $value,
                'type' => PDO::PARAM_STR)
            )
        );
    }

    protected function getMeta ($key)
    {
        $results = $this->__query("SELECT value FROM meta WHERE key=:key", array(
            'key'=>array(
                'val' => $key,
                'type' => PDO::PARAM_STR)
            )
        );
        if(count($results) !== 1)
        {
            RFAssert::Exception("ERROR! The number of results of meta query is not equal to one");
            return "";
        }
        return $results[0]['value'];
    }

    protected function setMeta($key, $value)
    {
        $this->__query("UPDATE meta SET value=:value WHERE key=:key", array(
            'key'=>array(
                'val' => $key,
                'type' => PDO::PARAM_STR),
            'value'=> array(
                'val' => $value,
                'type' => PDO::PARAM_STR)
            )
        );
    }

    protected function loadDataIntoTable ()
    {
        $data = $this->getData();
        $schema = $this->getSchema();

        foreach($data as $row)
        {
            $preparedQuery = "INSERT INTO data VALUES (\n";
            $placeholders = array();
            $valIndex = 0;
            $bindList = array();
            foreach ($schema as $key => $dtype)
            {
                $value = $row[$key];
                $valIndex++;
                $valKey = "val".$valIndex;
                $placeholders []= ":$valKey";

                $bindedVal = "NULL";
                $bindedType = PDO::PARAM_NULL;


                if($value === null) {
                    $bindedVal= "NULL";
                }
                if($dtype['type'] === "text")
                {
                    $bindedVal = $value;
                    $bindedType = PDO::PARAM_STR;

                }
                else if ($dtype['type'] === "number")
                {
                    $bindedVal = $value;
                    $bindedType = PDO::PARAM_INT;

                }
                else if ($dtype['type'] === "datetime")
                {
                    if(is_int($value)) {
                        $bindedVal = strftime("%F %T", $value);
                    }
                    else if(is_string($value)) {
                        $bindedVal = strftime("%F %T", strtotime($value));
                    }
                    else {
                        // TODO: Use $value->format instead
                        $bindedVal = strftime("%F %T", $value->getTimestamp());
                    }
                    $bindedType = PDO::PARAM_STR;
                }
                else {
                    RFAssert::Exception("Unknown type");
                }
                $bindList[$valKey] = array(
                    'val' => $bindedVal,
                    'type' => $bindedType
                );
            }
            $preparedQuery .= implode(",\n", $placeholders);
            $preparedQuery .= "\n);";

            $this->__query($preparedQuery, $bindList);
        }

        $this->setSQLSource('data');
    }

    protected abstract function getSchema();
    protected abstract function getData();
}

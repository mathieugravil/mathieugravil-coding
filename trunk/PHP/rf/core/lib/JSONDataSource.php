<?php

class JSONDataSource extends RFHttpDataSource{
    protected $schema;
    protected $filePath;

    /**
     * Set the Schema for the DataSource.
     * Schema is an array of fields and the types, options associated with them
     *
     * Example ::
     * array (
     *     'FieldName' => array('type' => 'text'),
     *     'FieldName2' => array('type' => 'number')
     * );
     * 
     * @param array $schema Schema for the DataSource
     */
    public function setSchema ($schema)
    {
        $this->schema = $schema;
    }

    /**
     * Get the schema for the DataSource
     * @internal 
     * @return Array 
     */
    protected function getSchema()
    {
        return $this->schema;
    }


    protected $dataObjPath;
    /**
     * @param string $dataObjPath
     */
    
   
    /**
     * If your JSON data is nested deeper than the root level, you can specify that by
     * setting the data object path. So for example, if your JSON string is ::
     *
     *    {
     *        'comment': 'Accessed on 3-1-2013',
     *        'data_container': {
     *            'description': 'Contents of our data',
     *            'data': {
     *                // the actual data you want
     *            }
     *        }
     *    }
     *
     * Then the data object path is ``data_container.data``
     * @param string $dataObjPath 
     */
    public function setDataObjectPath ($dataObjPath) {
        $this->dataObjPath = $dataObjPath;
    }

    /**
     * @internal
     */
    public static function __findFromPath ($obj, $path) {
        $pathList = explode('.', $path);
        foreach($pathList as $pathItem) {
            if(!isset($obj[$pathItem])) {
                return null;
            }
            $obj= $obj[$pathItem];
        }
        return $obj;
    }

    /**
     * Gets the data from the specified source
     * @internal
     * @return Array 
     */
    protected function getData()
    {
        if(isset($this->filePath)){
            $rawObj = json_decode(file_get_contents($this->filePath), true);
        }
        else if(isset($this->url)) {
            $rawObj = json_decode($this->makeRequest(), true);
        }


        if(isset($this->dataObjPath)) {
            $rawObj = self::__findFromPath($rawObj, $this->dataObjPath);
        }

        $schema = $this->getSchema();
        $data = array();
        foreach($rawObj as $row) {
            $newRow = array();
            foreach($schema as $key => $item) {
                $val = null;
                if(isset($item['path'])) {
                    $val = self::__findFromPath($row, $item['path']);
                }
                else {
                    if(isset($row[$key])) {
                        $val = $row[$key];
                    }
                }
                if($item['type'] === _RF_DTYPE_TIME) {
                    if(is_string($val))
                    {
                        // Parse the string as a value
                        $val = strtotime($val);
                    }

                }
                $newRow [$key] = $val;
            }
            $data []= $newRow;
        }

        return $data;
    }

    /**
     * Set the URL from which the JSON string will be loaded
     * @param string $url 
     */
    public function setUrl($url) {
        parent::setUrl($url);
    }

    /**
     * If you are loading from a JSON file on your server, then provide the full path to the file.
     * @param string $path 
     */
    public function setFilePath($path) {
        $this->filePath = $path;
    }

    public function __construct() {
        $this->inMemory = false;
    }
}
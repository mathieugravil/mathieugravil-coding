<?php

class ArrayDataSource extends RFCachedDataSource {
	public function setStructuredData($data) {
		$this->data = $data;
	}
	public function setSchema($schema) {
		$this->schema = $schema;
	}

	public function __construct() {
		// by default, use in memory SQLite
		parent::__construct();
		$this->inMemory = true;
	}

    protected function getSchema() {
    	return $this->schema;
    }
    protected function getData() {
    	return $this->data;
    }

    public function setData($structureArray, $data) {
        $structure = array_keys($structureArray);
        
        $this->schema = array();        
        foreach($structureArray as $key => $value) {
            if(is_string($value)) {
                $this->schema[$key] = array(
                    'type' => $value
                );
            }
        }
        
    	$colCount = count($structure);
    	$this->data = array();
    	$dataLen = count($data);
    	for($i = 0; $i < $dataLen; $i++) {
    		$newRow = array();
    		for($j = 0; $j < $colCount; $j++) {
    			if($structure[$j] == null) {
    				continue;
    			}
    			if(isset($data[$i][$j])) {
    				$newRow [$structure[$j]] = $data[$i][$j];
    			}
    			else {
    				RFAssert::Exception("Missing data at Row $i, Col $j [".$structure[$j]."]");
    			}
    		}
    		$this->data []= $newRow;
    	}
    }

	protected $data;
	protected $schema;
}
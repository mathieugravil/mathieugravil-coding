<?

class XMLDataSource extends RFHttpDataSource {
	protected $schema;
	protected $filePath;
	protected $dataObjNamespace;
	protected $dataObjPath;

	/**
	 * Set the Schema for the DataSource.
	 * Schema is an array of fields and the types, options associated with them
	 *
	 * Example ::
	 *   array (
	 *     'FieldName1' => array('type' => 'text'),
	 *     'FieldName2' => array('type' => 'number')
	 *   );
	 * 
	 * @param array $schema Schema for the DataSource
	 */
	public function setSchema ($schema) {
		$this->schema = $schema;
	}

	/**
	 * Get the schema for the DataSource
	 * @internal 
	 * @return Array 
	 */
	protected function getSchema () {
		return $this->schema;
	}

	/**
	 * If your JSON data is nested deeper than the root level, you can specify that by
	 * setting the data object path. So for example, if your JSON string is ::
	 *
	 * <book>
	 *   <description> Books Section </description>
	 *   <section_id>3234324</section_id>
	 *   <item>
	 *   	<title>Book 1</title>
	 *   	<link>http://foobar/book1</link>
	 *   <item>
	 *   <item>
	 *   	<title>Book 2</title>
	 *   	<link>http://foobar/book2</link>
	 *   </item>
	 * </book>
	 * Then the data object path is ``/book/item``
	 * 
	 * @param string $path      
	 * @param string $namespace 
	 */
	public function setDataObjPath($path, $namespace=NULL) {
		$this->dataObjPath = $path;
		if(isset($namespace))
			$this->dataObjNamespace = $namespace;
	}

	/**
	 * Gets the data from the specified source
	 * @internal
	 * @return Array 
	 */
	protected function getData() {
		if(isset($this->filePath)){
			$rawXMLString = file_get_contents($this->filePath);
		}
		else if(isset($this->url)) {
			$rawXMLString = $this->makeRequest();
		}
		$xmlTree = new SimpleXMLElement($rawXMLString);
        
		if(isset($this->dataObjNamespace)) {
			$xmlTree->registerXPathNamespace('objNS', $this->dataObjNamespace);
			$xpath = "//objNS:" . substr($this->dataObjPath, 1);
			$dataObj = $xmlTree->xpath($xpath);
		}
		else {
			$dataObj = $xmlTree->xpath(substr($this->dataObjPath, 1));
		}

        $outputData = array();
        $schema = $this->getSchema();

        foreach($dataObj as $row) {
            $newRow = array();
            
            foreach($schema as $key => $item) {
                // get the value at the item['xpath']
             	if(isset($item['namespace'])) {
             		$row->registerXPathNamespace('rowNS', $item['namespace']);
             		$rowXpath = "rowNS:" . $item['xpath'];
             		$resultArray = $row->xpath($rowXpath);
             		foreach ($resultArray as $result) {
						$res = each($result);
						$value = $res['value'];
             		}
             	} 
             	else {
             		$resultArray = $row->xpath($item['xpath']);
             		foreach ($resultArray as $result) {
             			$res = each($result);
             			$value = $res['value'];
             		};

             	}
                $newRow[$key] = $value;
            }

            $outputData []= $newRow;
        }
        return $outputData;

	}
	
	/**
	 * If you are loading from a XML file on your server, then provide the full path to the file.
	 * @param string $filePath 
	 */	
	public function setFilePath ($filePath) {
		$this->filePath = $filePath;
	}

	/**
	 * Set the URL from which the XML string will be loaded
	 * @param string $url 
	 */
	public function setUrl ($url) {
		parent::setUrl($url);
	}
}
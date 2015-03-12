<?php

class ExcelDataSource extends RFCachedDataSource {

	protected $cols = array();
	public function columnFromRange ($id, $rangeText, $type, $options = array()) {
		$this->cols[$id] = array(
			'id' => $id,
			'rangeText' => $rangeText,
			'type' => $type,
			'options' => $options
		);
	}

	// an array containing excel data sources, so that we can verify
	// that all of them have files.
	protected static $edsList = array();

	protected static function checkWhetherOtherUploadsNeeded () {
		foreach(self::$edsList as $ds) {
			// Even if a single other file needs uploading still,
			// then flag it as false so that the clientside window
			// will not close
			if(!$ds->checkIfFileExists()) {
				return true;
			}
		}
		// the control will only come here in case all files have been uploaded
		return false;
	}

	public static function fromFile($fileName, $options = array()) {
		$ds = new ExcelDataSource();
		$ds->setFileName($fileName);

		return $ds;
	}

	public static function fromUpload ($id, $options = array()) {
        $ds = new ExcelDataSource();
        $ds->id = $id;
        $ds->setUploadParams($id, $options);

        self::$edsList []= $ds;

        return $ds;
	}

    protected function setUploadParams ($id, $options) {
    	$this->checkIfFileExists();
    	RFMessageBroker::listenForMessage($id.'_upload', $this, '__getUpload');
    	RFMessageBroker::bind('beforeDashboardEmit', $this, '__injectRequirements');
    }

    protected $id = "";
    protected $needSpreadsheet = true;
    public function __injectRequirements () {
    	RFLog::log("Injecting requirements for ".$this->id);
    	$fileExists = $this->checkIfFileExists();

    	$requestArray = array(
    		'id' => $this->id,
    		'fileExists' => false,
    		'fileName' => ''
		);

		if($fileExists) {
			$requestArray['fileExists'] = true;
			$requestArray['fileName'] = $this->fileName;
		}
    	RFMessageBroker::sendMessageToDashboard('spreadsheetRequired', $requestArray);

    	return true;
    }

    public function __getUpload () {
    	RFLog::log("Getting the upload now", $_FILES);

    	$fileKey = 'spreadsheet_'.$this->id;

    	if (!isset($_FILES[$fileKey])) {
    		RFAssert::Exception("Was expecting spreadsheet ".$this->id." but couldn't find it");
    	}

    	// there is a file in the file variable right now for this handler
    	$fileDetails = $_FILES[$fileKey];
    	$fileName = $fileDetails['name'];
    	RFLog::log("The filename is ", $fileName);
    	if(substr($fileName, -4) !== ".xls" && substr($fileName, -5) !== ".xlsx") {
    		RFLog::log("Got something that isn't an excel file");
    		RFUtil::emitJSON(array('parseResult' => 'fail', 'message' => "Only excel files are allowed"));
    	}

    	// TODO: Do the acutal parsing.
    	$this->setFileFound($fileName);

    	$moreFilesRequired = self::checkWhetherOtherUploadsNeeded();

        RFUtil::emitJSON(array('parseResult' => 'success',
        	'readyForDashboard' => !$moreFilesRequired
    	));
    }

    protected function setFileFound ($fileName) {
    	RFLog::log("Flagging ".$this->id." as found");
    	$_SESSION['hasfile_'.$this->id] = true;
    	$_SESSION['fileName_'.$this->id] = $fileName;

    	$extension = ".xlsx";
    	if(substr($fileName, -4) !== ".xls") {
    		$extension = ".xls";
    	}

    	// now save the file to the disk
    	$excelFilename = ''.session_id().$this->id.$extension;
    	$filePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.$excelFilename;

    	RFLog::log("Moving uploaded file to ", $filePath);
    	$tmp_name = $_FILES["spreadsheet_".$this->id]["tmp_name"];
    	move_uploaded_file($tmp_name, $filePath);
    	$_SESSION['filePath_'.$this->id] = $filePath;

    }

    protected function checkIfFileExists () {
    	RFLog::log("Checking if a file already exists for ".$this->id);
    	if(isset($_SESSION['hasfile_'.$this->id])) {
    		RFLog::log("Session has been set");
    		if($_SESSION['hasfile_'.$this->id] === true) {
    			RFLog::log("Found file for ".$this->id);
    			$this->setFileName($_SESSION['filePath_'.$this->id]);
    			return true;
    		}
    	}
    	RFLog::log("No file exists");
    	return false;
    }

	protected $excelObject;
	protected $fileName;
	protected function setFileName ($fileName) {
		RFLog::log("Setting an excel file path of ", $fileName);
		$this->fileName = $fileName;
		$this->addInvalidator($fileName);
	}

	public function getSchema() {
		$schema = array();

		foreach($this->cols as $id => $col) {
			$schema [$id] = array('type' => $col['type']);
		}

		return $schema;
	}

	public function getData () {
		RFLog::createProfiler("Excel Import");
        /** @var $reader PHPExcel_Reader_Abstract */
		$reader = PHPExcel_IOFactory::createReaderForFile($this->fileName);
		RFLog::addStep("Created Object");
		$reader->setReadDataOnly(true);

        /** @var $excelObject PHPExcel */
		$this->excelObject = $reader->load($this->fileName);
		RFLog::addStep("Loaded File");

		//
		// 'id' => array(1, 2, 3, 4, 5)
		$data = array();

		$worksheet = $this->excelObject->getActiveSheet();
		RFLog::addStep("Got active sheet");

		$rowCount = 0;


		foreach($this->cols as $id => $col) {
			RFLog::addStep("Starting New Column ".$col['rangeText']);
			$columnData = $worksheet->rangeToArray($col['rangeText'], null, false, false, false);
			RFLog::addStep("Got Range to Array");

			if($rowCount === 0) {
				$rowCount = count($columnData);
			}
			else {
				if($rowCount !== count($columnData)) {

					// TODO: Be more clear and show exact range that caused the issue
					RFAssert::Exception("Error, mismatched number of rows");
				}
			}

			$cleanData = array();

			foreach($columnData as $colItem) {
				$item = $colItem[0];

				$result = $item;
				if($col['type'] === "string") {
					if($item === null) {
						$result = null; // TODO: an empty string or text "null" or what?
					}
				}
				else if ($col['type'] === "number") {
					if($item === null) {
						$result = null;
					}
					else  {
						if(is_numeric($item)) {
							$result = floatval($item);
						}
						else {
							// TODO: try to parse it better
							
							// Ultimately,
							RFAssert::Exception("The item $item doesn't seem to be a number");
						}
					}
				}
				else if ($col['type'] === "time") {
					if($item === null) {
						$result = null;
					}
					else  {
						var_dump($item);
						$result = strtotime($item);
					}
				}

				$cleanData []= $result;
			}

			$data[$id] = $cleanData;
			RFLog::addStep("Stored Data");
		}

		RFLog::logProfiler();

		// Now we need to flip it around
		// array('id1' => val1, 'id2' => val2);
		
		$finalData = array();

		for($i = 0; $i < $rowCount; $i ++)
		{
			$row = array();

			foreach($this->cols as $id => $col) {
				// TODO: there is a more efficient way of doing this.
				$row[$id] = $data[$id][$i];
			}

			$finalData []= $row;
		}


		return $finalData;
	}

	public function initialize () {
		RFLog::log("Trying to initialize excel data source");
		if(isset($this->fileName)) {
			RFLog::log("File exists, initializing now");
			parent::initialize();
		}
	}

    public function __getDataSourceNotReadyMessage () {
        return "Please <a class='rfExcelSelect'>select an Excel file</a> to see data.";
    }
}
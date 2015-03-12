<?php

class RFArrayDataSource extends RFCachedDataSource {
	public function setData($data) {
		$this->data = $data;
		$this->tryInitialize();
	}

	public function setSchema ($schema) {
		$this->schema = $schema;
		$this->tryInitialize();
	}

	protected function getSchema() {
		return $this->schema;
	}

    protected function getData() {
    	return $this->data;
    }

    protected function tryInitialize () {
    	if(isset($this->data) && isset($this->schema))
    	{
    		$this->initialize();
    	}
    }

	protected $data;
	protected $schema;
}

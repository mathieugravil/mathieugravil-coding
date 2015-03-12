<?php

abstract class RFHttpDataSource extends RFCachedDataSource {

    protected $url;

    protected function setUrl ($url) {
        $this->addInvalidator($url);
        $this->url = $url;
    }

    protected $postData = array();

    protected $headers = array();

    /**
     * Set the data to be sent along with a POST request.
     * The POST data should be an associative array.
     *
     * @param  array $postData 
     */
    public function setPostData ($postData = array()) {
        $this->addInvalidator($postData);
        $this->postData = $postData;
    }

    /**
     * Set the custom headers to the request. 
     * Pass the headers as a key-value pair.
     *
     * @param array $headers 
     */
    public function setHeaders ($headers = array()) {
        $this->addInvalidator($headers);
        $this->headers = $headers;
    }

    protected $method = 'get';

    /**
     * Set the HTTP request method type
     *
     * @param  string $method 
     */
    public function setMethod ($method) {
        $this->addInvalidator($method);
        $this->method = $method;
    }

    protected $options = array();

    /**
     * Set the options for fetching the data.
     * @internal
     * @param array|\RFHttpOptions $options
     */
    public function setOptions ($options = array()) {
        $options = new RFHttpOptions($options);
        $this->options = $options->asArray();
        $this->addInvalidator($this->options);
    }

    protected function makeRequest () {
        if($this->method === "get") {
            $response = Requests::get($this->url, $this->headers, $this->options);
        }
        else if ($this->method === "post") {
            $response = Requests::post($this->url, $this->headers, $this->options);
        }

        /** @var $response Requests_Response*/
        if($response->success) {
            return $response->body;
        }
        else {
            $url = $this->url;
            RFAssert::Exception("The request to $url was not successful. Details: ".var_export($response));
            return "";
        }
    }

    public function __construct () {
        parent::__construct();
        $this->inMemory = true;
    }
}

class RFHttpOptions extends RFOptions {
    /**
     * How long should we wait for a response?
     *
     * @var int
     */
    public $timeout = 10;

    /**
     * The useragent to send to the server
     *
     * @var string
     */
    public $useragent = "RazorFlowPHP/HTTPDataSourceFetcher";

    /**
     * Should we follow 3xx redirects?
     *
     * @var bool
     */
    public $follow_redirects = true;

    public function __inheritedClassName()
    {
        return __CLASS__;
    }
}
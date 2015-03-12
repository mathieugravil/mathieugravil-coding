<?php

abstract class RFRPC {
    public function __construct () {

    }

    public function Handle() {
        RFRequest::assertExists($this->__getParamKey());

        $req = RFRequest::get($this->__getParamKey());
        RFAssert::HasKey("Request must be valid", "name", $req);
        RFAssert::HasKey("Request must be valid", "params", $req);

        RFAssert::InArray("Method name must be valid", $req['name'], $this->__getMethodList());

        $name = $req['name'];
        $params = $req['params'];

        $this->$name ($params);
    }

    public abstract function __getParamKey ();
    public abstract function __getMethodList ();
}
<?php

class RFMessageBroker
{
    protected static $dbMessages = array();
    public static function sendMessageToDashboard($type, $params) {
        self::$dbMessages []= array(
            'type' => $type,
            'params' => $params
        );
    }

    protected static $listeners = array();
    public static function listenForMessage ($endpoint, $object, $method, $unsafe = false) {
        if(!isset(self::$listeners[$endpoint])){
            self::$listeners[$endpoint] = array();
        }

        self::$listeners[$endpoint] []= array(
            'object' => $object,
            'method' => $method,
            'unsafe' => $unsafe
        );
    }

    public static function bind($endpoint, $object, $method) {
        self::listenForMessage($endpoint, $object, $method);
    }

    public static function bindUnsafe($endpoint, $object, $method) {
        self::listenForMessage($endpoint, $object, $method, true);
    }

    public static function getMessages () {
        return self::$dbMessages;
    }

    public static function routeMessage($endpoint, $unsafe = false) {
        $params = array();
        if(RFRequest::check('messageParams')) {
            $params = json_decode(RFRequest::get('messageParams'));
        }
        $result = self::trigger($endpoint, $params, $unsafe);
    }

    public static function routeMessageUnsafe ($endPoint) {
        self::routeMessage($endPoint, true);
    }

    public static function trigger($endPoint, $params = array(), $unsafe = false) {
        if(isset(self::$listeners[$endPoint])) {
            foreach(self::$listeners[$endPoint] as $listener) {
                $obj = $listener['object'];
                $method = $listener['method'];

                call_user_func_array(array($obj, $method), array($params));
            }
        }
    }
}

<?php

class RFJson {
    private static $singleton;

    /**
     * @static
     * @return RFJson
     */
    public static function instance()
    {
        if(!isset(RFJson::$singleton))
        {
            RFJson::$singleton = new RFJson();
        }
        return RFJson::$singleton;
    }

    private $tabString = "  ";

    private function __construct(){}

    public function Serialize($object)
    {
//        return json_encode($object);
        return $this->_Serialize($object);
    }

    public function _Serialize ($object, $root = true)
    {
        $result = "";

        if(is_numeric($object))
        {
            if(is_string($result))
            {
                // TODO: Check for a "." in the string and change to floatval instead
                // of intval

                $result = ''.intval($object);
            }
            else
                $result = ''.$object;
        }
        else if(is_string($object))
        {
            // escape quotes and put between ""
            $result = '"'.str_replace('"', '\"',$object).'"';
            $result = str_replace(PHP_EOL, '\n',$result);
        }
        else if(is_array($object))
        {
            $root = false; // even if it's a root object, don't add [] or {}
            if(isset($object[0]) || count($object) === 0) // numeric array
            {
                $result = "[";
                $count = count($object);
                $i = 0;
                foreach($object as $val)
                {
                    $i ++;
                    $valJson = $this->indentText($this->Serialize($val, false));
                    $result = $result."\n".$this->tabString.$valJson;
                    if($i !== $count) // unless it's the last record, add a comma
                        $result = $result.',';
                }
                if(count($object) !== 0)
                    $result = $result."\n]";
                else
                    $result = $result."]";
            }
            else
            {
                $result = "{";
                $count = count($object);
                $i = 0;
                foreach($object as $key => $val)
                {
                    $i ++;
                    $valJson = $this->indentText($this->Serialize($val, false));
                    $result = $result."\n".$this->tabString."\"".$key."\":".$valJson;
                    if($i !== $count)
                        $result = $result.',';
                }
                $result = $result."\n}";
            }
        }
        else if(is_bool($object))
        {
            if($object)
                $result = "true";
            else
                $result = "false";
        }
        else if ($object instanceof fTimestamp)
        {
            $result = $object->format("U");
        }
        else if ($object instanceof ColorRange)
        {
            $result = $object->toString();
        }
        else if (method_exists($object, "__serialize"))
        {
            $result = $this->Serialize($object->__serialize());
        }
        else if(is_null($object))
        {
            $result = "null";
        }
        else
        {
            $msg = "";
            $msg = print_r($object, true);
            RFAssert::Exception("TODOERROR: Unknown type in FRJSon $msg");
        }

        if($root)
            $result = "{$result}";

        return $result;
    }

    private function indentText ($text)
    {
        return implode("\n".$this->tabString, explode("\n", $text));

    }
}



<?php

class RFAssert {
    public static function Internal ($message, $debug = false)
    {
        if(!RFRequest::isHTMLRequest())
        {
            $ex = new RFInternalException($message);

            RFUtil::Exception($ex);
        }
    }

    public static function Exception ($message, $debug = false)
    {
        if(!RFRequest::isHTMLRequest())
        {
            $ex = new RFException($message);

            RFUtil::Exception($ex);
        }
    }

    public static function Assert($message, $value)
    {
        if($value === TRUE)
            return;

        self::Exception("Error ($message)");
    }

    public static function InArray($message, $value, $array)
    {
        if(in_array($value, $array))
            return;

        self::Exception("Error ($message) Expected - one of : " . implode(', ', $array) . ". Got: $value");
    }

    public static function AllInArray($message, $values, $array)
    {
        foreach($values as $value)
        {
            RFAssert::InArray($message, $value, $array);
        }
    }

    public static function AllKeysInArray($message, $values, $array)
    {
        foreach($values as $value)
        {
            RFAssert::HasKey($message, $value, $array);
        }
    }

    public static function IsSubsetOfArray($message, $input, $array)
    {
        $diff = array_diff($input, $array);

        if(count($diff) === 0)
        {
            return;
        }

        self::Exception("Error ($message) Expected - inside : " . implode(', ', $array) . ". Outliers - " . implode(', ', $diff));
    }

    public static function Equals ($message, $expected, $actual)
    {
        if($expected === $actual)
        {
            return;
        }

        self::Exception("Error ($message) Expected $expected. Got $actual instead.");
    }

    public static function HasKey ($message, $key, $array)
    {
        if(isset($array[$key]))
        {
            return;
        }
        self::Exception("Error($message) Expected $key to be inside array", $array);
    }

    public static function IsArray($item)
    {
        if(is_array($item))
        {
            return;
        }
        self::Exception("Error. Expecting an array, but instead, got - ", $item);
    }

    public static function IsA($message, $object, $class)
    {
        if(is_a($object, $class))
            return;

        self::Exception("Error ($message). Expected an instance of $class. Instead got - ", $object);
    }

    public static function MatchRegexp ($message, $value, $regexp) {
        // TODO: implement
        // 
    }
}














abstract class _RFException extends Exception
{

}

class RFInternalException extends _RFException
{

}

class RFException extends _RFException
{

}

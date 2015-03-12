<?php

abstract class RFOptions
{
    /**
     * Create an instance of this particular RFOptions object
     *
     * @param array $input
     */
    public function __construct($input = array()) {
        $defaults = array();

       if(isset(Dashboard::$__optDefaults[$this->__inheritedClassName()]))
       {
           $defaults = Dashboard::$__optDefaults[$this->__inheritedClassName()];
       }

        if(count($defaults) > 0)
        {
            $propNames = $this->__getPropNames();

            RFAssert::IsSubsetOfArray("The configuration properties must be valid", array_keys($defaults), $propNames);

            foreach($defaults as $key => $value)
            {
                $this->$key = $value;
            }
        }

        if(is_array($input))
        {
            // Create a new target instance
            $myName = $this->__inheritedClassName();
            $propNames = $this->__getPropNames();

            RFAssert::IsSubsetOfArray("The configuration properties must be valid", array_keys($input), $propNames);

            foreach($input as $key => $value)
            {
                $this->$key = $value;
            }
        }
        else if (is_object($input))
        {
            /** @var $input RFOptions */
            if(method_exists($input, '__inheritedClassName'))
            {
                $myName = $this->__inheritedClassName();
                $targetName = $input->__inheritedClassName();

                if($myName === $targetName)
                {
                    $targetProp = $input->asArray();

                    foreach($targetProp as $key => $value)
                    {
                        $this->$key = $value;
                    }
                }
                // RFAssert::Exception("Cannot consume $targetName from $myName");
            }
        }
    }

    /**
     * Get the entire option class as an array. For internal use only
     * @ignore
     * @return array
     */
    public function asArray()
    {
        $propNames = $this->__getPropNames();
        $output = array();

        foreach($propNames as $name)
        {
            if(isset($this->$name))
            {
                $val = $this->$name;

                if(is_object($val))
                {
                    if(method_exists($val, 'asArray'))
                    {
                        $val = $val->asArray();
                    }
                    else
                    {
                        RFAssert::Exception("Error: Trying to serialize something for Options which cannot be serialized");
                    }
                }

                $output [$name] = $val;
            }
        }

        $ignoreList = $this->getIgnoreList();
        foreach($ignoreList as $ignoreItem)
        {
            unset($output[$ignoreItem]);
        }

        return $output;
    }


    /**
     * Return the inherited class name
     *
     * @abstract
     */
    public abstract function __inheritedClassName ();


    /**
     * Override this function and return a list of keys that need to be ignored
     * from the array
     *
     * @return array
     */
    protected function getIgnoreList() {
        return array();
    }

    /**
     * Get a list of all the property names in thie class
     *
     * @return array
     */
    public function __getPropNames ()
    {
        $ref = new ReflectionClass($this->__inheritedClassName());

        $propNames = array();

        $props = $ref->getProperties(ReflectionProperty::IS_PUBLIC);

        /** @var $prop ReflectionProperty */
        foreach($props as $prop)
        {
            $propNames []= $prop->getName();
        }

        return $propNames;
    }
}

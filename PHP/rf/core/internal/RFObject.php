<?php
$GLOBALS["idtable"] = array();

abstract class RFObject {
    /**
     * The ID of the object.
     *
     * Every instance of the inherited cass created in a request will have
     * a different ID
     *
     * However, IDs are sequentially generated. So It's certain that a script
     * with multiple instances of a class
     *
     * @var string
     */
    public $ID;

    /**
     * The string containing the object type
     * @var string
     */
    protected $objectType;

    /**
     * An abstract function that specifies the object type
     * The object type is a base string used to generate the IDs
     *
     * @abstract
     * @return string
     */
    public abstract function getObjectType();

    /**
     * @return string
     */
    public function getID ()
    {
        return $this->ID;
    }

    /**
     * Only to be used in exceptional circumstances to change the ID of an object
     *
     * @param $id
     */
    public function __forceOverrideID ($id)
    {
        $this->ID = $id;
    }

    public function __construct()
    {
        // Find the object type
        $type = $this->getObjectType();

        // Use an ID Table. Used to store the number of instances of this class
        if(!isset($GLOBALS["idtable"][$type]))
            $GLOBALS["idtable"][$type] = 0;

        // set the current id by using the index of number of Instances
        $this->ID = $type.'_'.$GLOBALS["idtable"][$type];
        $this->objectType = $type;

        // Increment the index, so next object will have a different ID
        $GLOBALS["idtable"][$type] ++;
    }
}

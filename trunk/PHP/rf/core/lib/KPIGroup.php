<?php

class BulletGraphGroup extends RFKpiComponentGroup {
    public function initialize()
    {
        parent::initialize();
    }

    public function __construct()
    {
        parent::__construct();
        $this->displayMode = "BulletGroup";
        $this->chromeless = true;
    }


}


class KpiTableComponent extends RFKpiComponentGroup {
    public function initialize()
    {
        parent::initialize();
    }

    public function __construct()
    {
        parent::__construct();
        $this->displayMode = "KpiTable";
        $this->chromeless = true;
    }
}


<?php

class KPIComponent extends RFKpiComponentCore {

    protected function initialize()
    {
        parent::initialize();
    }


    public function __construct()
    {
        parent::__construct();
        $this->setOption('__displayMode', "KpiComponent");

        // Set default dimensions
        $this->height = 1;
        $this->width = 1;

    }
}

class GaugeComponent extends RFKpiComponentCore {


    protected function initialize()
    {
        $init = parent::initialize();

        if($init)
            return $init;
        

        return false;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setOption('__displayMode', "GaugeComponent");

        // Set default dimensions
        $this->height = 1;
        $this->width = 1;
    }
}

class KPITableComponent extends RFKpiGroupComponent {
    public function __construct() {
        parent::__construct();

        $this->displayMode = "KpiTableComponent";

    }
}

class VBulletGroupComponent extends RFKpiGroupComponent {
    public function __construct() {
        parent::__construct();

        $this->displayMode = "VBulletGroupComponent";

    }
}

class HBulletGroupComponent extends RFKpiGroupComponent {
    public function __construct() {
        parent::__construct();

        $this->displayMode = "HBulletGroupComponent";

    }
}

class GroupedGaugeComponent extends RFKpiGroupComponent {
    public function __construct() {
        parent::__construct();

        $this->displayMode = "GroupedGaugeComponent";

    }
}

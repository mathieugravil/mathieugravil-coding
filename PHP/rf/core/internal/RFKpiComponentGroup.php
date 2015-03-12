<?php

class KPIGroupComponentOptions extends ComponentOptions {
    public function __inheritedClassName(){
        return __CLASS__;
    }
}

abstract class RFKpiGroupComponent extends Component {
    public function addStaticKPI ($caption, $currentValue, $pastValues = array(), $kpiOptions = array())
    {
        $this->invalidate(func_get_args());

        $kpi = new KPIComponent();
        $kpi->caption = $caption;
        $kpi->setStaticCurrentValue($currentValue);
        $kpi->setStaticPastValues($pastValues);
        $kpi->setOption($kpiOptions);

        $this->kpiList []= $kpi;

    }

    public function addDynamicKPI ($caption, $valueExpression, $timestampExpression = "", $kpiOptions = array(), $valueOptions = array(), $timestampOptions = array())
    {
        $this->assertDataSourceLinked();
        $this->invalidate(func_get_args());

        $kpi = new KPIComponent();
        $kpi->setDataSource($this->_ds);
        $kpi->caption = $caption;
        $kpi->setValueExpression($valueExpression, $valueOptions);
        $kpi->setTimestampExpression($timestampExpression, $timestampOptions);
        $kpi->setOption($kpiOptions);

        $this->kpiList []= $kpi;
    }

    public function addKPIComponent ($kpic)
    {
        $this->kpiList []= $kpic;
    }

    public function setTarget($value, $name)
    {
        $this->target = $value;
        $this->targetName = $name;
    }

    public function setSparkLineOptions($opt)
    {
        $this->sparkLineOpt = new SparkLineOptions($opt);
    }

    /**
     * @param ColorRange $range
     */
    public function setColorRanges ($range)
    {
        $this->colorRanges = $range;
    }

    protected $target;

    protected $targetName;

    protected $kpiList = array();

    protected $displayMode;

    protected $sparkLineOpt;

    /**
     * @var ColorRange
     */
    protected $colorRanges;

    protected function initialize ()
    {
        $init = parent::initialize();
        if($init)
            return $init;

        $this->properties['childKpis'] = array();

        if(isset($this->colorRanges))
        {
            $this->properties['colorRange'] = $this->colorRanges->asArray();
        }

        if(isset($this->target))
        {
            $this->properties['target'] = $this->target;
            $this->properties['targetName'] = $this->target;
        }
        
        if(isset($this->sparkLineOpt))
        {
            $this->properties['sparkLineOpt'] = $this->sparkLineOpt->asArray();
        }

        /** @var $kpi RFKpiComponentCore */
        foreach($this->kpiList as $kpi)
        {
            $this->properties['childKpis'] []= $kpi->__getProperties();
        }

        $this->properties['displayMode'] = $this->displayMode;

        return false;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setCaching();

    }

    public function getObjectType()
    {
        return __CLASS__;
    }
}

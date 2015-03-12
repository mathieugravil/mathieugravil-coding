<?php

class FunnelComponent extends Component
{
    /**
     * Set the label colmumn to derive the label column for the X-Axis
     * @param $labelColumn
     */
    public function setLabelColumn ($labelColumn)
    {
        $this->assertDataSourceLinked();

        // add to cache invalidators
        $this->invalidate(func_get_args());

        $this->dbLabelColumn = $labelColumn;
    }

    public function setValueExpression ($valueExp , $options = array())
    {
        $this->assertDataSourceLinked();

        // add to cache invalidators
        $this->invalidate(func_get_args());

        $this->valueExp = $valueExp;
        $this->valueOpt = $options;
    }

    /**
     * Set the order of labels to be displayed in the funnel
     * @param array $order
     */
    public function setFunnelOrder($order)
    {
        $this->assertDataSourceLinked();

        $this->invalidate(func_get_args());

        $this->funnelOrder = $order;

    }

    /**
     * Main entry point for the component
     *
     * @return bool
     */
    protected function initialize()
    {
        $init = parent::initialize();

        if($init)
            return $init;

        $this->internalChart->caption = "Foo Caption";
        $this->internalChart->setLabelColumn($this->dbLabelColumn);
        $this->internalChart->addSeries("Value", $this->valueExp, $this->valueOpt);

        // set the funnel order which will be picked up by the js and handled
        $this->internalChart->setOption('__funnelOrder', $this->funnelOrder);

        // patch this component's properties with the internal chart's properties
        $this->properties = $this->internalChart->__getProperties();

        $this->dataset = $this->internalChart->dataset;
        $this->dataset->ID = $this->getID();
        $this->properties['dsId'] = $this->getID();
        $this->properties['id'] = $this->getID();
        Dashboard::__getSingleton()->unregisterDataSet($this->internalChart->getID());
        Dashboard::__getSingleton()->addDataSet($this->dataset);

        return false;
    }

    protected $dbLabelColumn;

    protected $valueExp, $valueOpt;

    protected $funnelOrder;

    /**
     * @var ChartComponent
     */
    protected $internalChart;

    public function __getChildComponents(){
        return array($this->internalChart);
    }

    /**
     * Override the postinitialize so that the options of the internal chart are
     * not overwritten by an empty array options of the funnel on post
     *
     * @return bool
     */
    protected function postInitialize()
    {
        return false;
    }

    public function __construct()
    {
        parent::__construct();
        $this->internalChart = new ChartComponent();
    }




    public function getObjectType()
    {
        return 'ChartComponent';
    }
}

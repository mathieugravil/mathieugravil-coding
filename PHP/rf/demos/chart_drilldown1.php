<?php
require "../razorflow.php";
Dashboard::setTitle("Drill-down in same chart");

$dataSource = new SQLiteDataSource("databases/birt.sqlite");
$dataSource->setSQLSource("payments JOIN customers ON payments.customerNumber = customers.customerNumber");

$drillCategory = new ChartComponent();
$drillCategory->setWidth(4);
$drillCategory->setCaption("Drill-down by category", "Sales for {{value}}");
$drillCategory->setYAxis("Sales", array('numberPrefix' => "$"));
$drillCategory->setDataSource($dataSource);
$drillCategory->setLabelExpression("Location", "customers.country", array(
	'drillPath' => array('customers.country', 'customers.state', 'customers.city', 'customers.customerName')
));
$drillCategory->addSeries("Sales", "payments.amount", array(
	'displayType' => 'Column'
));
Dashboard::addComponent($drillCategory);

$drillTime = new ChartComponent();
$drillTime->setCaption("Drill-down by time", "Sales for {{value}}");
$drillTime->setYAxis("Sales", array('numberPrefix' => "$"));
$drillTime->setWidth(4);
$drillTime->setDataSource($dataSource);
$drillTime->setLabelExpression("Time", "payments.paymentDate", array(
	'timestampRange' => true,
	'autoDrill' => true
));
$drillTime->addSeries("Sales", "payments.amount", array(
	'displayType' => 'Column'
));
Dashboard::addComponent($drillTime);

Dashboard::Render();
<?php
require "../razorflow.php";

$dataSource = new SQLiteDataSource("databases/birt.sqlite");
$dataSource->setSQLSource("orderdetails JOIN orders ON orders.orderNumber = orderDetails.orderNumber
						   JOIN products ON orderdetails.productCode = products.productCode 
						   JOIN customers ON orders.customerNumber = customers.customerNumber");


$shipStatus = new ChartComponent();
$shipStatus->setCaption("Shipment Status");
$shipStatus->setDataSource($dataSource);
$shipStatus->setYAxis("Cancelled Orders");
$shipStatus->setSecondYAxis("Cancelled Orders");
$shipStatus->setLabelExpression("Order Date", "orders.orderDate", array(
	'timestampRange' => true
));
$shipStatus->addSeries("Shipped Orders", "orders.orderNumber", array(
	'aggregateFunction' => "COUNT",
	'condition' => "orders.status = 'Shipped'"
));
$shipStatus->addSeries("Cancelled Orders", "orders.orderNumber", array(
	'aggregateFunction' => "COUNT",
	'condition' => "orders.status = 'Cancelled'",
	'onSecondYAxis' => true

));
Dashboard::addComponent($shipStatus);

Dashboard::Render();
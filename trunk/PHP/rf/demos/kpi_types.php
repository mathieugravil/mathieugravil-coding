<?php
require "../razorflow.php";
Dashboard::setTitle("KPI types supported in RazorFlow PHP");
$dataSource = new SQLiteDataSource("databases/northwind.sqlite");
$dataSource->setSQLSource("order_details
	JOIN orders ON orders.OrderID = order_details.OrderID 
	JOIN products ON products.ProductID = order_details.ProductID 
	JOIN categories ON categories.CategoryID = products.CategoryID
	JOIN employees ON orders.EmployeeID = employees.EmployeeID
	JOIN customers ON customers.CustomerID = orders.CustomerID");

$monthlySales = new KPIComponent();
$monthlySales->setCaption("Average Monthly Sales");
$monthlySales->setDataSource($dataSource);
$monthlySales->setValueExpression("order_details.UnitPrice * order_details.Quantity", array(
	'aggregate' => true,
	'aggregateFunction' => "AVG",
	'numberPrefix' => "$"
));
$monthlySales->setTimestampExpression("OrderDate", array(
	'timeUnit' => 'month'
));
Dashboard::addComponent($monthlySales);

$monthlyQty = new KPIComponent();
$monthlyQty->setCaption("Average Monthly Units");
$monthlyQty->setDataSource($dataSource);
$monthlyQty->setValueExpression("order_details.Quantity", array(
	'aggregate' => true,
	'aggregateFunction' => "AVG"
));
$monthlyQty->setTimestampExpression("OrderDate", array(
	'timeUnit' => 'month'
));
Dashboard::addComponent($monthlyQty);

$averageOrder = new GaugeComponent();
$averageOrder->setCaption("Average Order Value");
$averageOrder->setDataSource($dataSource);
$averageOrder->setValueExpression("order_details.UnitPrice * order_details.Quantity", array(
	'aggregate' => true,
	'aggregateFunction' => "AVG",
	'numberPrefix' => "$"
));
$averageOrder->setTimestampExpression("OrderDate", array(
	'timeUnit' => 'month'
));
$averageOrder->setKeyPoints(array(0, 300, 600, 1000));
$averageOrder->setOption('showLatestOnly', true);
Dashboard::addComponent($averageOrder);

$averageQty = new GaugeComponent();
$averageQty->setCaption("Average Order Units");
$averageQty->setDataSource($dataSource);
$averageQty->setValueExpression("order_details.Quantity", array(
	'aggregate' => true,
	'aggregateFunction' => "AVG"
));
$averageQty->setTimestampExpression("OrderDate", array(
	'timeUnit' => 'month'
));
$averageQty->setKeyPoints(array(0, 10, 20, 30));
$averageQty->setOption('showLatestOnly', true);
Dashboard::addComponent($averageQty);

Dashboard::Render();

<?php
// The Executive Dashboard
// =======================
// 
// In this example, you'll build a dashboard that can be used to get a
// quick overview of the NorthFlow store.
// 
// It'll contain the following functionality:
// 
// 1. Indicators of the overall average order value and quantity.
// 2. This month's order value and quantity, along with a basic historical indicator
// 3. Charts to show the following:
//    * Top 5 Products
//    * Top 5 Employees
//    * Top 5 Customers
//    * Top 5 Categories
// 
// First thing to do is to require `razorflow.php` and set the title of the Dashboard
require "../razorflow.php";
Dashboard::setTitle("RazorFlow KPI Dashboard");

// Next, you will create a DataSource. The DataSource here is a SQLite Datasource and you'll be
// using the :php:class:`SQLiteDataSource` class to create a DataSource instance.
// 
// The data will be taken from the order_details table, which is also JOINed with the
// 
// 1. ``products`` table, which contains information on the products - ProductName, etc.
//    This will be used for determining top products
// 2. ``categories`` table, which contains the category names of the products.
//    This will be used to determine the top categories.
// 3. ``employees`` table containing the employee information. Each order will get
//    full details about the employee that sold the item, helping determine the top
//    employees.
// 4. ``customers`` table, which contains information on all the customers. Each row in 
// o  ``order_details`` will have the customer information attached to it.
$dataSource = new SQLiteDataSource("databases/northwind.sqlite");
$dataSource->setSQLSource("order_details
	JOIN orders ON orders.OrderID = order_details.OrderID 
	JOIN products ON products.ProductID = order_details.ProductID 
	JOIN categories ON categories.CategoryID = products.CategoryID
	JOIN employees ON orders.EmployeeID = employees.EmployeeID
	JOIN customers ON customers.CustomerID = orders.CustomerID");

// The first component you're going to add is a :php:class:`GaugeComponent`, to show the average
// order value.
// 
// you create the component object, set the caption, and the DataSource
$averageOrder = new GaugeComponent();
$averageOrder->setCaption("Average Order Value");
$averageOrder->setDataSource($dataSource);

// Now, you set a value expression, as the "unit price of each order * quantity of the order",
// which results in the cost of each order.
// 
// Hoyouver, since you're interested in the average order value, you have to aggregate it.
// It might seem a little odd, since "aggregate" usually implies the total values, but 
// here, it's similar to writing the SQL Query ``SELECT AVG(UnitPrice * Quantity) AS value FROM order_details``
// 
// you're also passing an array of options, with the following parameters (see :ref:`options_shorthand` for more information )
// 
// 1. ``'aggregate'=>true`` - Aggregate all the records into one record
// 2. ``'aggregateFunction'=>'AVG'`` - perform an Average. (you can also use other SQL functions
// like MAX, MIN, STDDEV, etc)
// 3. ``'numberPrefix'=>'$'`` - Since this is a currency value, prefix a "$" before showing the figure
// 
// Also, you're using :php:ref:`GaugeComponent::setKeyPoints` to set the "key points" which are
// points in the gauge betyouen which there are different ranges of color
$averageOrder->setValueExpression("order_details.UnitPrice * order_details.Quantity", array(
	'aggregate' => true,
	'aggregateFunction' => "AVG"
));
$averageOrder->setOption('numberPrefix', "$");
$averageOrder->setKeyPoints(array(0, 300, 600, 1000));
Dashboard::addComponent($averageOrder);

// Next, you will add a gauge which shows the average order quantity.
// 
// Add another gauge component which works exactly like the previous one. The only difference
// is that you'll be using ``order_details.Quantity`` to determine the value. Since you're
// only interested in the average quantity. Also, there is no number prefix.
// 
$averageQty = new GaugeComponent();
$averageQty->setCaption("Average Order Quantity");
$averageQty->setDataSource($dataSource);
$averageQty->setValueExpression("order_details.Quantity", array(
	'aggregate' => true,
	'aggregateFunction' => "AVG"
));
$averageQty->setKeyPoints(array(0, 10, 20, 30));
Dashboard::addComponent($averageQty);

// Add a component to show the monthly sales as a Key Performance Indicator
// 
// Create a :php:class:`KPIComponent` and set the caption and the DataSource.
// You also set the value expression as ``UnitPrice * Quantity`` which gives the 
// total cost of each order. And pass options to:
// 
// * Aggregate the value expression - ``'aggregate'=>true``
// * Ensure that records are added -  ``'aggregateFunction'=>"SUM"``
// * Display a "$" before showing the value - ``'numberPrefix'=>'$'``
// 
// Afterwards, use :php:meth:`KPIComponent::setTimestampExpression` to set the
// timestamp expression. The timestamp expression is the column which specifies the
// timestamp of each order. In this case, it's ``OrderDate``
// 
// Also, you pass an option to ``setTimestampExpression``, setting the ``timeUnit`` to 
// 'month', so that records are grouped by month.
// 
// Similarly, create another KPI to show the monthly quantity and add it as a component.
$monthlySales = new KPIComponent();
$monthlySales->setCaption("Last Month's Sales");
$monthlySales->setDataSource($dataSource);
$monthlySales->setValueExpression("order_details.UnitPrice * order_details.Quantity", array(
	'aggregate' => true,
	'aggregateFunction' => "SUM",
	'numberPrefix' => "$"
));
$monthlySales->setTimestampExpression("OrderDate", array(
	'timeUnit' => 'month'
));
Dashboard::addComponent($monthlySales);

$monthlyQty = new KPIComponent();
$monthlyQty->setCaption("Last Month's Quantity");
$monthlyQty->setDataSource($dataSource);
$monthlyQty->setValueExpression("order_details.Quantity", array(
	'aggregate' => true,
	'aggregateFunction' => "SUM"
));
$monthlyQty->setTimestampExpression("OrderDate", array(
	'timeUnit' => 'month'
));
Dashboard::addComponent($monthlyQty);

// We are interested in knowing the top few performing:
// 
// * Products
// * Employees
// * Customers
// * Countries
// 
// These are available in different tables in the database. However, since we performed 
// multiple JOINs while creating the DataSource, all of them are available in the same
// DataSource.
// 
// The DataSource consists of sales records. However, since we're interested in finding 
// top Products, Employees, etc, we need a corresponding SQL Expression to get the name
// of the Product, Employee, etc, from each row in the sales records.
// 
// In order to demonstrate the flexibility of RazorFlow Dashboards for PHP, we will
// take a non-conventional approach to adding the components for the Top 5 Products,
// Employees, Customers, etc.
// 
// Since adding and configuring the component is identical for each of the metrics, except for
// the name, and the expression used to find the value, we can put this information in a separate array
// 
// We create an array of four metrics 
$topMetrics = array(
	array('Products', "Product", "products.ProductName"),
	array('Employees', "Employee", "employees.FirstName || ' ' || employees.LastName"),
	array('Customers', "Customer", "customers.CompanyName"),
	array('Countries', "Country", "customers.Country")
);

// Now, we use a ``foreach`` loop to iterate through each of the top metrics
// 
// Each of the metrics is an array with 3 items:
// 1. The caption
// 2. The name of the X-Axis
foreach($topMetrics as $item)
{
	$caption = $item[0];
	$xAxis = $item[1];
	$labelExp = $item[2];

// Now, we start building the actual chart. We create a ChartComponent 
// in the Component Object ``$topItems``.
// 
// First, we're going to set a caption, the DataSource, the Y Axis
// (and ensure that all figures on the Y Axis has a '$' prefixed to them
// because they denote monetary value).
// 
// We are also setting a secondary Y Axis using ``setSecondYAxis`` to denote
// the quantity for each item. The Secondary Y Axis is useful because we are
// showing two measures on this chart - Sales and Quantity which have two 
// different scales.
	$topItems = new ChartComponent();
	$topItems->setCaption("Top 5 $caption");
	$topItems->setDataSource($dataSource);
	$topItems->setYAxis("Sales", array('numberPrefix' => '$'));
	$topItems->setSecondYAxis("Quantity");
	$topItems->setDimensions(2, 2);

// Next, you'll set the label expression. In short, the label expression
// is used to determine the labels/X-Axis values in the chart.
// 
// The expression set in the chart is derived from the array that we 
// declared earlier. For example, while building the chart of the top
// Products, the call to this function will look like::
// 
//     $topItems->setLabelExpression("Products", "products.ProductName");
// 
// Which ensures that the names of the top products are displayed on the X-Axis
// 
// Afterwards, we add two series:
// 
// 1. A series for total sale value of each product, which is derived form the SQL 
//    Expression ``order_details.UnitPrice * order_details.Quantity``. You're 
//    passing an array of options, but with only one option - ``'sort'=>'DESC'``,
//    which ensures that the list is sorted in descending order.
// 2. A series for the total quantity sold for each product. Here, the
//    values are derived from a SQL Expression ``order_details.Quantity``, 
//    and again, you're passing an array of options, with a single option -
//    ``'onSecondYAxis'=>true`` - which ensures that the chart is on the 
//    Secondary Y Axis.
	$topItems->setLabelExpression ($xAxis, $labelExp);
	$topItems->addSeries("Sales", "order_details.UnitPrice * order_details.Quantity", array(
		'sort' => "DESC"
	));
	$topItems->addSeries("Quantity", "order_details.Quantity", array(
		'onSecondYAxis' => true
	));

// Set two options on the Chart Component object:
// 
// 1. ``'limit'=>5`` - Show only 5 items in the chart.
// 2. ``'showValues'=>false`` - Don't show the values for each column in
//    in the chart. Your users will still be able to find the values,
//    but displaying all the values will make the overall display of the chart
//    cluttered and unusable
//    
// And then add this component to the dashboard. Note that since you're calling
// ``Dashboard::addComponent`` inside the loop, it's actually called four times
// - one for each metric in the ``$topMetrics`` array.
	$topItems->setOption(array(
		'limit' => 5,
		'showValues' => false
	));
	Dashboard::addComponent($topItems);
}

// And finally, Render the dashboard.
Dashboard::Render();
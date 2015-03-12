<?php
require "../razorflow.php";
Dashboard::setTitle("Management Dashboard for NorthFlow Traders");
// Dashboard::setFooterText("Built with the <a href='http://northwinddatabase.codeplex.com/'>NorthWind Sample Database</a>");
$dataSource = new SQLiteDataSource("databases/northwind.sqlite");
$dataSource->setSQLSource("order_details 
    JOIN orders ON orders.OrderID = order_details.OrderID 
    JOIN products ON products.ProductID = order_details.ProductID 
    JOIN categories ON categories.CategoryID = products.CategoryID
    JOIN customers ON customers.CustomerId = orders.CustomerId
    JOIN employees ON employees.EmployeeId = orders.EmployeeId");


$yearFilter = new ConditionFilterComponent();
$yearFilter->setDimensions(1, 1);
$yearFilter->setDataSource($dataSource);
$yearFilter->setCaption("Select a Year");
$yearFilter->addSelectCondition("Year", array("1996", "1997", "1998"), array(
    "strftime('%Y', orders.OrderDate) = '1996'",
    "strftime('%Y', orders.OrderDate) = '1997'",
    "strftime('%Y', orders.OrderDate) = '1998'"
));
Dashboard::addComponent($yearFilter);

$averageOrderValue = new KPIComponent();
$averageOrderValue->setCaption("Average Order Value");
$averageOrderValue->setDataSource($dataSource);
$averageOrderValue->addInitialCondition("strftime('%Y', orders.OrderDate)", "=", "1998");
$averageOrderValue->setDimensions(1, 1);
$averageOrderValue->setTimestampExpression("orders.OrderDate", array(
    'timeUnit' => "month"
));
$averageOrderValue->setValueExpression("order_details.UnitPrice * order_details.Quantity", array(
    'numberPrefix' => "$",
    'valueFontSize' => 50
));
Dashboard::addComponent($averageOrderValue);

$averageUnitCount = new KPIComponent();
$averageUnitCount->setCaption("Average Units/Order");
$averageUnitCount->setDataSource($dataSource);
$averageUnitCount->addInitialCondition("strftime('%Y', orders.OrderDate)", "=", "1998");
$averageUnitCount->setDimensions(1, 1);
$averageUnitCount->setTimestampExpression("orders.OrderDate", array(
    'timeUnit' => "month"
));
$averageUnitCount->setValueExpression("order_details.Quantity", array(
    'aggregateFunction' => "AVG",
    'numberSuffix' => " units",
    'valueFontSize' => 40
));
Dashboard::addComponent($averageUnitCount);

$averageDelayInShipping = new GaugeComponent();
$averageDelayInShipping->setCaption("Average Delay in Shipping");
$averageDelayInShipping->setDataSource($dataSource);
$averageDelayInShipping->addInitialCondition("strftime('%Y', orders.OrderDate)", "=", "1998");
$averageDelayInShipping->setDimensions(1, 1);
$averageDelayInShipping->setValueExpression("strftime('%d', orders.ShippedDate) - strftime('%d', orders.RequiredDate)", array(
    'aggregateFunction' => "AVG",
    'numberSuffix' => " days"
));
$averageDelayInShipping->setKeyPoints(array(-10, -5, 0, 10, 15));
Dashboard::addComponent($averageDelayInShipping);

$topCountriesChart = new ChartComponent();
$topCountriesChart->setDimensions(2, 2);
$topCountriesChart->setCaption("Top 5 countries");
$topCountriesChart->setDataSource($dataSource);
$topCountriesChart->addInitialCondition("strftime('%Y', orders.OrderDate)", "=", "1998");
$topCountriesChart->setYAxis("Revenue", array('numberPrefix' => '$'));
$topCountriesChart->setSecondYAxis("Quantity");
$topCountriesChart->setLabelExpression("Country", "customers.Country");
$topCountriesChart->addSeries("Revenue", "order_details.UnitPrice * order_details.Quantity", array(
    'displayType' => "Column",
    'sort' => "DESC"
));
$topCountriesChart->addSeries("Quantity", "order_details.Quantity", array(
    'displayType' => "Line",
    'onSecondYAxis' => true
));
$topCountriesChart->setOption('limit', 5);
$topCountriesChart->setOption('showValues', false);
Dashboard::addComponent($topCountriesChart);



$topEmployeesChart = new ChartComponent();
$topEmployeesChart->setDimensions(2, 2);
$topEmployeesChart->setCaption("Sales per Employee");
$topEmployeesChart->setDataSource($dataSource);
$topEmployeesChart->addInitialCondition("strftime('%Y', orders.OrderDate)", "=", "1998");
$topEmployeesChart->setYAxis("Revenue", array('numberPrefix' => '$'));
$topEmployeesChart->setLabelExpression("Employee Name", "Employees.FirstName");
$topEmployeesChart->addSeries("Sales", "order_details.UnitPrice * order_details.Quantity", array(
    'displayType' => "Pie", 
    'numberPrefix' => "$",
    'sort' => "DESC"
));
$topEmployeesChart->setOption('limit', 5);
Dashboard::addComponent($topEmployeesChart);

$categoryList = array(
    'Beverages' => 1,
    'Condiments' => 2,
    'Confections' => 3,
    'Diary Products' => 4,
    'Grains/Cereals' => 5,
    'Meat/Poultry' => 6,
    'Produce' => 7,
    'Seafood' => 8
);

$categoryChart = new ChartComponent();
$categoryChart->setWidth(4);
$categoryChart->setYAxis("Sales", array('numberPrefix' => "$"));
$categoryChart->setCaption("Category Wise Sales");
$categoryChart->setDataSource($dataSource);
$categoryChart->addInitialCondition("strftime('%Y', orders.OrderDate)", "=", "1998");
$categoryChart->setLabelExpression("Time", "orders.OrderDate", array(
	'timestampRange' => true,
	'timeUnit'=> 'month'
));
foreach($categoryList as $category => $catID) {
    $categoryChart->addSeries($category, "order_details.UnitPrice * order_details.Quantity", array(
        'condition'=> "categories.CategoryID = $catID"
    ));
}
$categoryChart->setOption('showValues', false);
Dashboard::addComponent($categoryChart);

$shipDelayChart = new ChartComponent();
$shipDelayChart->setWidth(4);
$shipDelayChart->setDataSource($dataSource);
$shipDelayChart->addInitialCondition("strftime('%Y', orders.OrderDate)", "=", "1998");
$shipDelayChart->setYAxis("Days");
$shipDelayChart->setCaption("Average Delay in Shipping");
$shipDelayChart->setLabelExpression("Country", "orders.ShipCountry");
$shipDelayChart->addSeries("Delay", "strftime('%d', orders.ShippedDate) - strftime('%d', orders.RequiredDate)", array(
    'aggregateFunction' => "AVG"
));
Dashboard::addComponent($shipDelayChart);


$yearFilter->addFilterTo($topCountriesChart);
$yearFilter->addFilterTo($topEmployeesChart);
$yearFilter->addFilterTo($categoryChart);
$yearFilter->addFilterTo($shipDelayChart);
$yearFilter->addFilterTo($averageOrderValue);
$yearFilter->addFilterTo($averageUnitCount);
$yearFilter->addFilterTo($averageDelayInShipping);

Dashboard::Render();


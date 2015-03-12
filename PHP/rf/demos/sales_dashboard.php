<?php

require "../razorflow.php";
Dashboard::setTitle("Sales Dashboard");

$dataSource = new SQLiteDataSource("databases/birt.sqlite");
$dataSource->setSQLSource("orders
JOIN customers ON orders.customerNumber = customers.customerNumber 
JOIN orderdetails ON orderdetails.orderNumber = orders.orderNumber
JOIN products ON products.productCode = orderdetails.productCode
JOIN productlines ON productlines.productLine = products.productLine");



#Chart Component
$salesTrend = new ChartComponent();
$salesTrend->setCaption( "Sales Trends", "Sales Trends in {{value}}");
$salesTrend->setDataSource($dataSource);
$salesTrend->setLabelExpression("Year", "orders.orderDate", array(
	'timestampRange' => 'time',
	'timeUnit' => 'year',
	'customTimeUnitPath' => array('year', 'month', 'day'),
	'autoDrill' => true
));
$salesTrend->addSeries("Vintage Cars", "orderdetails.quantityOrdered", array(
	'condition' => "products.productLine = 'Vintage Cars'",
));

$salesTrend->addSeries("Motorcycles", "orderdetails.quantityOrdered", array(
	'condition' => "products.productLine = 'Motorcycles'"
));

$salesTrend->addSeries("Classic Cars", "orderdetails.quantityOrdered", array(
	'condition' => "products.productLine = 'Classic Cars'"
));
$salesTrend->addSeries("Trucks & Buses", "orderdetails.quantityOrdered", array(
	'condition' => "products.productLine = 'Trucks and Buses'"
));
$salesTrend->setOption('showValues', false);
$salesTrend->setYAxis("Amount", array('numberPrefix' => "$"));

Dashboard::addComponent($salesTrend);
#Pie Chart
$salesPie = new ChartComponent();
$salesPie->setCaption("Revenue by Country");
$salesPie->setYAxis("Sales", array('numberPrefix' => "$ "));
$salesPie->setDataSource($dataSource);
$salesPie->setLabelExpression("Country", "customers.country", array(
	'drillPath' => array('customers.country', 'customers.state', 'customers.city', 'customers.customerName')
));
$salesPie->addSeries("Sales", "orderdetails.quantityOrdered", array(
	'sort' => "DESC",
	'displayType' => "Pie",
	'pieChartLabelDisplay' => 'value'
));
$salesPie->setOption(array(
	'showLabels' => 0,
	'showValues' => 0,
	'showLegend' => 1,
	'legendPosition' => 'right'
));
Dashboard::addComponent($salesPie);

#function to get YTD
function getYTD() {
	$dbhandle = new PDO('sqlite:databases/birt.sqlite');
    
    $result = $dbhandle->query("select sum(orderdetails.quantityOrdered*orderdetails.priceEach) as ytdsum
              from orderdetails join  orders on orderdetails.orderNumber = orders.orderNUmber 
              where strftime('%Y', orders.orderDate) = '2004' and strftime('%m', orders.orderDate) < '06';");
    foreach ($result as $row) {
        $prevSales = $row['ytdsum'];
    }    

    $result = $dbhandle->query("select sum(orderdetails.quantityOrdered*orderdetails.priceEach) as ytdsum
              from orderdetails join  orders on orderdetails.orderNumber = orders.orderNUmber 
              where strftime('%Y', orders.orderDate) = '2005' and strftime('%m', orders.orderDate) < '06';");
    foreach ($result as $row) {
        $currentSales = $row['ytdsum'];
    }    

    $ytd = (($currentSales - $prevSales)/$currentSales)*100;
    return $ytd; 
}

#KPI Component
$YTDChange = new GaugeComponent();
$YTDChange->setCaption("YTD Change");
$YTDChange->setDimensions(1,1);
$YTDChange->setStaticCurrentValue(getYTD());
$YTDChange->setKeyPoints(array(0, 20, 40, 60, 80, 100));
$YTDChange->addTarget(60, "Target");
Dashboard::addComponent($YTDChange);

#KPI Component
$carsSoldKPI = new GaugeComponent();
$carsSoldKPI->setCaption("Cars sold this year");
$carsSoldKPI->setDimensions(1,1);
$carsSoldKPI->setDataSource($dataSource);
$carsSoldKPI->setTimestampExpression("orders.OrderDate", array('timeUnit' => 'year'));
$carsSoldKPI->setValueExpression("orderdetails.QuantityOrdered");
Dashboard::addComponent($carsSoldKPI);


#Table component
$salesTable = new TableComponent();
$salesTable->setCaption("Recent Purchases");
$salesTable->setDataSource($dataSource);
$salesTable->addColumn("Date", "orders.orderDate", array(
	'width' => 80,
	'sort' => 'DESC'
));
$salesTable->addColumn("Order ID", "orders.orderNumber", array(
	'width' => 65
));
$salesTable->addColumn("Customer ID", "customers.customerNumber", array(
	'width' => 80
));
$salesTable->addColumn("Sales Rep ID", "customers.salesRepEmployeeNumber", array(
	'width' => 65
));
$salesTable->addColumn("Product ID", "products.productName", array(
	'width' => 65
));
$salesTable->addColumn("Quantity", "orderdetails.quantityOrdered", array(
	'width' => 60
));
$salesTable->addColumn("Each Price", "orderdetails.priceEach", array(
	'numberPrefix' => ' $'
));

Dashboard::addComponent($salesTable);

#Filter component
$salesFilter = new AutoFilterComponent();
$salesFilter->setDataSource($dataSource);
$salesFilter->setHeight(1);
$salesFilter->setCaption("Filter Recent Purchases");
$salesFilter->addMultiSelectFilter("Select Product", "products.productName");
$salesFilter->addTimeRangeFilter("Order Date", "orders.orderDate");
Dashboard::addComponent($salesFilter);

$salesFilter->addFilterTo($salesTable);

Dashboard::render();

<?php
require "../razorflow.php";

Dashboard::setTitle("NorthFlow Traders - Inventory Dashboard");

$dataSource = new SQLiteDataSource("databases/northwind.sqlite");
$dataSource->setSQLSource("Products JOIN categories ON categories.CategoryID = Products.CategoryID");

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

foreach ($categoryList as $categoryName => $categoryID) {
	$catKPI = new KPIComponent();
	$catKPI->setCaption("$categoryName");
	$catKPI->setDataSource($dataSource);
	$catKPI->setValueExpression("UnitsInStock", array(
		'aggregate' => true,
		'aggregateFunction' => "SUM",
		'numberSuffix' => " units"
	));
	$catKPI->addCondition("Products.CategoryID", "=", $categoryID);
	Dashboard::addComponent($catKPI);
}

$distributionOfStock = new ChartComponent();
$distributionOfStock->setCaption("Distribution of inventory by units");
$distributionOfStock->setDataSource($dataSource);
$distributionOfStock->setYAxis("Quantity", array('numberSuffix' => " units"));
$distributionOfStock->setLabelExpression("Category Name", "CategoryName");
$distributionOfStock->addSeries("Stock Quantity", "UnitsInStock", array(
	'displayType' => "Pie",
));
Dashboard::addComponent($distributionOfStock);

$distributionOfRevenue = new ChartComponent();
$distributionOfRevenue->setCaption("Distribution of inventory by value");
$distributionOfRevenue->setYAxis("Revenue", array('numberPrefix' => "$"));
$distributionOfRevenue->setDataSource($dataSource);
$distributionOfRevenue->setLabelExpression("Category Name", "CategoryName");
$distributionOfRevenue->addSeries("Stock Quantity", "UnitsInStock * UnitPrice", array(
	'displayType' => "Pie",
));
Dashboard::addComponent($distributionOfRevenue);

$productList = new TableComponent();
$productList->setCaption("List of items in stock");
$productList->setDataSource($dataSource);
$productList->setDimensions(2, 2);
$productList->addColumn("Product Name", "ProductName");
$productList->addColumn("Price", "UnitPrice", array('numberPrefix' => "$"));
$productList->addColumn("Units in Stock", "UnitsInStock");
$productList->addColumn("Units in Order", "UnitsOnOrder");
$productList->addColumn("Reorder Level", "ReorderLevel");
Dashboard::addComponent($productList);

$productFilter = new ConditionFilterComponent();
$productFilter->setCaption("Filter items in stock");
$productFilter->setDataSource($dataSource);
$productFilter->setDimensions(2, 2);
$categoryNames = array(); $categoryConditions = array();
foreach($categoryList as $categoryName => $categoryID) {
	$categoryNames []= $categoryName;
	$categoryConditions []= "Products.CategoryID = $categoryID";
}
$productFilter->addSelectcondition("Select Category", $categoryNames, $categoryConditions);
$productFilter->addTextContainsCondition("Product Name Contains", "ProductName LIKE {{value}}");
$productFilter->addCheckboxCondition("Low Stock", "UnitsInStock < ReorderLevel");
$productFilter->addCheckboxCondition("Exclude Discontinued Items", "Discontinued = 0");
Dashboard::addComponent($productFilter);

$productFilter->addFilterTo($productList);

Dashboard::Render();
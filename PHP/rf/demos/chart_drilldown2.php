<?php
require "../razorflow.php";
Dashboard::setTitle("Automatic drill-down to another component of the Dashboard");

$dataSource = RFUtil::getSampleDataSource();

// Create a chart to show aggregated sales by genre
$genreSales = new ChartComponent();
$genreSales->setCaption("Sales by Genre");
$genreSales->setWidth(4);
$genreSales->setYAxis("Units");
$genreSales->setDataSource($dataSource);
$genreSales->setLabelExpression("Genre", "genre.Name");
$genreSales->addSeries("Number of Units", "invoiceline.Quantity", array(
    'sort' => "DESC"
));
$genreSales->setOption('limit', 10);
Dashboard::addComponent($genreSales);

// Create a chart to show aggregated sales by artist
$artistSales = new ChartComponent();
$artistSales->setCaption("Sales By Artist", "Sales by artists in {{label}} Genre");
$artistSales->setWidth(4);
$artistSales->setPlaceholder("Please click on a genre in the chart above");
$artistSales->setYAxis("Units");
$artistSales->setDataSource($dataSource);
$artistSales->setLabelExpression("Artist", "artist.Name");
$artistSales->addSeries("Sales", "track.UnitPrice * invoiceline.Quantity", array(
	'sort' => "DESC",
	'displayType' => "Pie"
));
$artistSales->setOption('limit', 10); // Only display the top 10
Dashboard::addComponent($artistSales);

// Link the genre chart to
$genreSales->autoLink($artistSales);

Dashboard::Render();
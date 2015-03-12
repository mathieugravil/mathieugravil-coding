<?php
require "../razorflow.php";

$dataSource = RFUtil::getSampleDataSource();

// Create a chart to show aggregated sales by genre
$genreSales = new ChartComponent();
$genreSales->setCaption("Genre Sales");
$genreSales->setDataSource($dataSource);
$genreSales->setLabelExpression("Genre", "genre.Name");
$genreSales->addSeries("Sales", "track.UnitPrice * invoiceline.Quantity", array(
    'sort' => "DESC",
    'numberPrefix' => '$ '
));
$genreSales->setOption('limit', 10);
Dashboard::addComponent($genreSales);

// Create a chart to show aggregated sales by artist
$artistSales = new ChartComponent();
$artistSales->setCaption("Artist Sales");
$artistSales->setDataSource($dataSource);
$artistSales->setLabelExpression("Artist", "artist.Name");
$artistSales->addSeries("Sales", "track.UnitPrice * invoiceline.Quantity", array(
	'sort' => "DESC",
    'numberPrefix' => '$ '
));
$artistSales->setOption('limit', 10); // Only display the top 10
Dashboard::addComponent($artistSales);

// Link the genre chart to
$genreSales->autoLink($artistSales);

// Create a table component
$saleTable = new TableComponent();
$saleTable->setCaption("Sales Table");
$saleTable->setWidth(3);
$saleTable->setDataSource($dataSource);
$saleTable->addColumn("Track", "track.Name");
$saleTable->addColumn("Album", "album.Title");
$saleTable->addColumn("Sale Date", "invoice.InvoiceDate", array(
    'width'=>50
));
$saleTable->addColumn("Amount", "track.UnitPrice * invoiceLine.Quantity", array(
    'width'=>50,
    'textAlign'=>'right',
    'numberPrefix'=> '$'
));
Dashboard::addComponent($saleTable);

// Link the artist chart to the sales table
$artistSales->autoLink($saleTable);

// Create a Key Performance Indicators to measure the total sales last month
$saleKPI = new KPIComponent();
$saleKPI->setCaption("Last Month's sales");
$saleKPI->setDataSource($dataSource);
$saleKPI->setValueExpression("track.UnitPrice * invoiceLine.Quantity", array(
    'aggregate' => true,
    'aggregateFunction' => "SUM",
    'numberPrefix' => '$'
));
$saleKPI->setTimestampExpression("invoice.InvoiceDate", array(
    'timeUnit' =>'month'
));
Dashboard::addComponent($saleKPI);

// Link the artist chart to sales KPI
$artistSales->autoLink($saleKPI);

$yearlySalesGauge = new GaugeComponent();
$yearlySalesGauge->setCaption("This Year's sales");
$yearlySalesGauge->setPlaceholder("Please select an artist");
$yearlySalesGauge->setDataSource($dataSource);
$yearlySalesGauge->setValueExpression("track.UnitPrice * invoiceLine.Quantity", array(
    'aggregate' => true,
    'aggregateFunction' => "SUM",
    'numberPrefix' => "$"
));
$yearlySalesGauge->setTimestampExpression("invoice.InvoiceDate", array(
    'timeUnit' => 'year'
));
$yearlySalesGauge->setOption('upperLimit', 30);
$yearlySalesGauge->setKeyPoints(array(5, 10, 20, 30));
Dashboard::addComponent($yearlySalesGauge);

// Link the artist chart to the yearly sales gauge
$artistSales->autoLink($yearlySalesGauge);

Dashboard::Render();
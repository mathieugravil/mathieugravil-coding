<?php
require "../razorflow.php";
Dashboard::setTitle("Chart Types supported in RazorFlow PHP");
$dataSource = new SQLiteDataSource('databases/chinook.sqlite');
$dataSource->setSQLSource("InvoiceLine JOIN Invoice ON Invoice.InvoiceId = InvoiceLine.InvoiceId JOIN Track ON Track.TrackId = InvoiceLine.TrackId JOIN Album ON Track.AlbumId = Album.AlbumId JOIN Artist ON Album.ArtistId = Artist.ArtistId JOIN Genre ON Track.GenreId = Genre.GenreId");

$genreChart = new ChartComponent();
$genreChart->setCaption("Top 10 Genres");
$genreChart->setWidth(4);
$genreChart->setYAxis("Sales", array('numberPrefix' => '$ '));
$genreChart->setDataSource($dataSource);
$genreChart->setLabelExpression("Genre", "genre.Name");
$genreChart->addSeries("Sales", "Quantity * track.UnitPrice", array('sort' => "DESC"));
$genreChart->setOption('limit', 10);
Dashboard::addComponent($genreChart);



$unitsAreaChart = new ChartComponent();
$unitsAreaChart->setCaption("Units By Year");
$unitsAreaChart->setWidth(4);
$unitsAreaChart->setDataSource($dataSource);
$unitsAreaChart->setYAxis("Units", array(
	'adaptiveYMin' => true
));
$unitsAreaChart->setLabelExpression("Year", "InvoiceDate", array(
	'timestampRange' => true,
	'timeUnit' => 'year'
));
$unitsAreaChart->addSeries("Units", "Quantity", array(
	'displayType' => "Line",
	'decimals' => 0
));
Dashboard::addComponent($unitsAreaChart);

$revenueChart = new ChartComponent();
$revenueChart->setCaption("Revenue By Year");
$revenueChart->setWidth(4);
$revenueChart->setDataSource($dataSource);
$revenueChart->setYAxis("Revenue", array(
	'numberPrefix' => "$",
	'adaptiveYMin' => true
));
$revenueChart->setLabelExpression("Year", "InvoiceDate", array(
	'timestampRange' => true,
	'timeUnit' => 'year'
));
$revenueChart->addSeries("Revenue", "Quantity * Track.UnitPrice", array(
	'displayType' => "Area",
	'decimals' => 0
));
Dashboard::addComponent($revenueChart);

$genrePie = new ChartComponent();
$genrePie->setCaption("Unit Distribution by Genre");
$genrePie->setWidth(4);
$genrePie->setYAxis("Sales", array('numberPrefix' => '$ '));
$genrePie->setDataSource($dataSource);
$genrePie->setLabelExpression("Genre", "genre.Name");
$genrePie->addSeries("Sales", "Quantity", array(
	'sort' => "DESC",
	'displayType' => "Pie",
	'pieChartLabelDisplay' => 'percentage'
));
$genrePie->setOption(array(
	'showLabels' => 0,
	'showValues' => 0,
	'showLegend' => 1,
	'legendPosition' => 'right',
	'limit' => 5
));
Dashboard::addComponent($genrePie);

$genreDonut = new ChartComponent();
$genreDonut->setCaption("Unit Distribution by Genre");
$genreDonut->setWidth(4);
$genreDonut->setYAxis("Sales", array('numberPrefix' => '$ '));
$genreDonut->setDataSource($dataSource);
$genreDonut->setLabelExpression("Genre", "genre.Name");
$genreDonut->addSeries("Sales", "Quantity", array(
	'sort' => "DESC",
	'displayType' => "Doughnut",
	'pieChartLabelDisplay' => 'value'
));
$genreDonut->setOption(array(
	'showLabels' => 0,
	'showValues' => 0,
	'showLegend' => 1,
	'legendPosition' => 'right',
	'limit' => 5
));
Dashboard::addComponent($genreDonut);


$genreSales = new ChartComponent();
$genreSales->setCaption("Yearly sales for top 5 genres");
$genreSales->setDataSource($dataSource);
$genreSales->setWidth(4);
$genreSales->setYAxis("Sales", array(
	'numberPrefix' => "$"
));
$genreSales->setLabelExpression("Year", "InvoiceDate", array(
	'timestampRange' => true,
	'timeUnit' => 'year'
));

$genreSales->addSeries("Rock", "Quantity * Track.UnitPrice", array(
	'condition' => "genre.Name = 'Rock'"
));
$genreSales->addSeries("Latin", "Quantity * Track.UnitPrice", array(
	'condition' => "genre.Name = 'Latin'"
));
$genreSales->addSeries("Metal", "Quantity * Track.UnitPrice", array(
	'condition' => "genre.Name = 'Metal'"
));
$genreSales->addSeries("Alternative & Punk", "Quantity * Track.UnitPrice", array(
	'condition' => "genre.Name = 'Alternative & Punk'"
));
$genreSales->addSeries("Jazz", "Quantity * Track.UnitPrice", array(
	'condition' => "genre.Name = 'Jazz'"
));
Dashboard::addComponent($genreSales);

$lineColumnChart = new ChartComponent();
$lineColumnChart->setCaption("Revenue and Units by Year");
$lineColumnChart->setWidth(4);
$lineColumnChart->setDataSource($dataSource);
$lineColumnChart->setYAxis("Revenue", array(
	'adaptiveYMin' => true,
	'numberPrefix' => "$"
));
$lineColumnChart->setSecondYAxis("Units", array(
	'adaptiveYMin' => true
));
$lineColumnChart->setLabelExpression("Year", "InvoiceDate", array(
	'timestampRange' => true,
	'timeUnit' => 'year'
));
$lineColumnChart->addSeries("Revenue", "Quantity * Track.UnitPrice", array(
	'displayType' => "Column",
	'decimals' => 0
));
$lineColumnChart->addSeries("Units", "Quantity", array(
	'displayType' => "Line",
	'color' => '#087155',
	'decimals' => 0,
	'onSecondYAxis' => true
));
$lineColumnChart->setOption('showValues', false);
Dashboard::addComponent($lineColumnChart);


Dashboard::Render();

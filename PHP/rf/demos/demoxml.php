<?php

require "../razorflow.php";

Dashboard::setTitle("Twitter Trends using XML DataSource");

$xmlDS = new XMLDataSource();
$xmlDS->setUrl('https://search.twitter.com/search.rss?q=%23php&rpp=100&lang=en');
$xmlDS->setSchema(array(
	'Author' => array(
		'type' => 'text',
		'xpath' => 'author'
	),
	'Link' => array(
		'type' => 'text',
		'xpath' => 'link'
	),
	'Description' => array(
		'type' => 'text',
		'xpath' => 'description'
	),
	'created_at' => array(
		'type' => 'datetime',
		'xpath' => 'pubDate'
	)
)); 

//XPath to the actual data 
$xmlDS->setDataObjPath('/channel/item');
$xmlDS->enableCaching();
$xmlDS->initialize();

$table = new TableComponent();
$table->setDataSource($xmlDS);

function format_tweet($table, $input) {
	return "<a href=${input['link']}>[Link]</a>";
}

$table->addColumn("Username", "Author");
$table->addColumn("Tweet", "Description");
$table->addColumn("Time", "created_at", array('width' => 80));
$table->fetchColumn("link", "Link");
$table->addCustomHTMLColumn("Link", "format_tweet");

Dashboard::addComponent($table);

$chart = new ChartComponent();
$chart->setDataSource($xmlDS);
$chart->setLabelExpression("Time", "created_at", array('timeUnit'=>'hour', 'timestampRange' => true));
$chart->addSeries("# of Tweets", "*", array('aggregateFunction' => "COUNT"));

Dashboard::addComponent($chart);

$chart->autoLink($table);

Dashboard::render();
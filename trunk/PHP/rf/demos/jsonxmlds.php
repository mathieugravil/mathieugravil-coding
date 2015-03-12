<?php

require "../razorflow.php";

echo "This example depended on twitter's API v1, twitter have turned it off starting June 11th. This example will be updated in the next release";
die();

Dashboard::setTitle("Twitter Trends using JSON and XML DataSource");

//Create and Initialize the JSON DataSource
$jsonDS = new JSONDataSource();
$jsonDS->setUrl('https://search.twitter.com/search.json?q=%23php&rpp=100&lang=en');
$jsonDS->setSchema(array(
    'text' => array('type' => 'text'),
    'iso_language_code' => array('type' => 'text'),
    'from_user_name' => array('type' => 'text'),
    'created_at' => array('type' => 'datetime'),
    'from_user' => array('type' => 'text'),
    'id_str' => array('type' => 'text')
));
// The actual results are in another object called 'results'
$jsonDS->setDataObjectPath('results');
$jsonDS->enableCaching();
$jsonDS->initialize();

//Create and Initialize the XML DataSource
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

//Create a Table Component using JSON DataSource
$jsonTable = new TableComponent();
$jsonTable->setDataSource($jsonDS);
$jsonTable->setCaption("Twitter Trends - JSON DataSource");
function format_tweet ($jsonTable, $input) {
    return "<a href='http://twitter.com/${input['user_name']}/status/${input['tweet_id']}/'>[Link]</a>";
}

$jsonTable->addCustomHTMLColumn("Link", 'format_tweet', array('width' => 50));
$jsonTable->addColumn("User", "from_user_name", array('width' => 100));
$jsonTable->addColumn("Tweet", "text");
$jsonTable->addColumn("Tweet Time", "created_at");
$jsonTable->fetchColumn("user_name", 'from_user');
$jsonTable->fetchColumn("tweet_id", 'id_str');

Dashboard::addComponent($jsonTable);

//Create a table using XML DataSource
$xmlTable = new TableComponent();
$xmlTable->setDataSource($xmlDS);
$xmlTable->setCaption("Twitter Trends - XML DataSource");
function format_tweet_xml($xmlTable, $input) {
	return "<a href=${input['link']}>[Link]</a>";
}

$xmlTable->addColumn("Username", "Author");
$xmlTable->addColumn("Tweet", "Description");
$xmlTable->addColumn("Time", "created_at", array('width' => 80));
$xmlTable->fetchColumn("link", "Link");
$xmlTable->addCustomHTMLColumn("Link", "format_tweet_xml");

Dashboard::addComponent($xmlTable);

Dashboard::render();
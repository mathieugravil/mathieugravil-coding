<?php

require "../razorflow.php";
Dashboard::setTitle("Twitter Trends");

// Create a new Array Data Source
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

// Create a new Table
$table = new TableComponent();
// Set the Data Source to the one that we just created
$table->setDataSource($jsonDS);
// Show only the english tweets by checking the language code
// Add columns to add the user and the tweet message

function format_tweet ($table, $input) {
    return "<a href='http://twitter.com/${input['user_name']}/status/${input['tweet_id']}/'>[Link]</a>";
}
$table->addCustomHTMLColumn("Link", 'format_tweet', array('width' => 50));
$table->addColumn("User", "from_user_name", array('width' => 100));
$table->addColumn("Tweet", "text");
$table->addColumn("Tweet Time", "created_at");
$table->fetchColumn("user_name", 'from_user');
$table->fetchColumn("tweet_id", 'id_str');

Dashboard::addComponent($table);

$chart = new ChartComponent();
$chart->setDataSource($jsonDS);
$chart->setLabelExpression("Time", "created_at", array('timeUnit'=>'hour', 'timestampRange' => true));
$chart->addSeries("# of Tweets", "*", array('aggregateFunction' => "COUNT"));
Dashboard::addComponent($chart);

$chart->autoLink($table);

Dashboard::Render();

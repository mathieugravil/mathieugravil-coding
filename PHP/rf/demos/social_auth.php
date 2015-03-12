<?php
include "../razorflow.php";

Dashboard::setTitle("Chart Component");


// 
// For security purposes, we cannot include the Google Authentication Tokens
// in this demo file. You can view this dashboard in action at:
// 
// http://razorflow.com/rf/demos/social_auth.php
// 
// But to run it on your own server, you need to generate your own keys.
// 
// Please refer to the documentation here:
// http://razorflow.com/docs/manual/php/concepts/socialauth.php
// 
// -------------START DELETING HERE---------------------------------------
if(file_exists("../examples/social_auth_keys.php")) {
	require "../examples/social_auth_keys.php";
}
else {
	// 
	RFAssert::Exception("Please generate Google Authentication Tokens to view this demo. 

See source code for details");
}
// -------------END DELETING HERE-----------------------------------------

SocialAuth::setupGoogle(array(
	// SET THE TOKENS HERE
));
SocialAuth::allowGoogleDomains(array('gmail.com'));

SocialAuth::setLoginPageMessages(array(
		'loginTitle' => 'RazorFlow SocialAuth Demo',
		'loginBody' => 'You need to log with your GMail Account to view this dashboard. If you do not have a GMail account, you will not be able to view this dashboard. We will not store your email or login records.'
));

$chart = new ChartComponent();
$chart->setCaption("Quarterly Performance");
$chart->setYAxis("Sales");
$chart->setStaticLabels("Quarter", array("Q1", "Q2", "Q3", "Q4"));
$chart->addStaticSeries("Store A", array(55, 23, 44, 23));
$chart->addStaticSeries("Store B", array(12, 41, 37, 47));
Dashboard::addComponent($chart);

$table = new TableComponent();
$table->setCaption("Product Sales");
$table->addStaticColumn("Product Name");
$table->addStaticColumn("Quantity");
$table->addStaticColumn("Store");
$table->addStaticRow (array("Les Paul", 312, "A"));
$table->addStaticRow (array("Explorer", 421, "B"));
$table->addStaticRow (array("Telecaster", 113, "B"));
$table->addStaticRow (array("Stratocaster", 186, "A"));
Dashboard::addComponent($table);

Dashboard::Render();

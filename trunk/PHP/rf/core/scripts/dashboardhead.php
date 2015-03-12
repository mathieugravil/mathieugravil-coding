<?php
$staticRoot = RFConfig::get('webroot')."/static";
$dashboardUrl = RFRequest::getBuildableUrl();

// The bunch of things to be printed from window.{key} = value;
$windowSettings = array(
	'rfStaticRoot' => '"'.$staticRoot.'"',
	'dashboardUrl' => '"'.$dashboardUrl.'"',
	'rfScreenshotMode' => 'true',
	'rfMinified' => 'false',
	'rfDebugMode' => RFConfig::isSetAndTrue('debug') ? 'true' : 'false'
);

$items = RFRequest::getAssets();

$scripts1 = $items[0];
$scripts2 = $items[1];
$stylesheets = $items[2];

if(RFConfig::isSetAndTrue('rfdevel')) {
	$windowSettings ['rfMinified'] = 'false';

}
else {
	$windowSettings ['rfMinified'] = 'true';
}


echo "<script type='text/javascript'>\n";
foreach($windowSettings as $key => $value) {
	echo "window.$key = ".($value).";\n";
}
echo "window.rfScripts1 = ['".implode("','",$scripts1)."'];";
echo "window.rfScripts2 = ['".implode("','",$scripts2)."'];";
echo "window.rfStyles = ['".implode("','",$stylesheets)."'];";
echo "</script>";


if(RFConfig::isSetAndTrue('rfdevel')) {
?>
 <!-- <script src="http://192.168.1.118:5000/target/target-script-min.js#anonymous"></script> -->
<?php
}
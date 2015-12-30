<?php

define('PUN_ROOT', './');
require PUN_ROOT.'/config2.php';
require PUN_ROOT.'/include/fonctions.php';


//$url_backup='http://www.google.fr';
//$sql_action = file_get_contents($url_backup);
//echo $sql_action ;
//phpinfo();



/*
$ch = curl_init("74.125.195.94");
$fp = fopen("example_homepage.txt", "w");

curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
if(curl_exec($ch) === false)
{
    echo 'Erreur Curl : ' . curl_error($ch);
}
else
{
    echo 'L\'opération s\'est terminée sans aucune erreur';
}

curl_close($ch);
fclose($fp);
*/

$url  = "http://mathieugravil:7pulturA+@sports-mathieugravil.rhcloud.com/sports/backup.php";
$ch = curl_init($url);
$timeout = 10;
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
if(curl_exec($ch) === false)
{
    echo 'Erreur Curl : ' . curl_error($ch);
}
else
{
    echo 'OK';
	$data = curl_exec($ch);
	//echo $data;
	 $link=connect_db($db_host, $db_username, $db_password, $db_name);
	 $sql_actions = explode(";",$data);
	 foreach ($sql_actions as $sql_action){
		 echo $sql_action ;
		 echo "<br>";
	 mysql_query($sql_action);		 
	 }

	 mysql_close($link);
}



curl_close($ch);




?>
<!DOCTYPE html>
<html>
<body>
</body>
</html>
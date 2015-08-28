<html>
  <head>
   <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
       <title>seance modifiee</title>
  </head>

  <body >

<?php

define('PUN_ROOT', './');
require PUN_ROOT.'./config2.php';
require PUN_ROOT.'./include/fonctions.php';


if (isset($_GET['seance_id']))
{
	$seance_id=$_GET['seance_id'];
}
if (isset($_GET['seance_name']))
{
$seance_name=$_GET['seance_name'];
}
if (isset($_GET['sport_id']))
{
$sport_id=$_GET['sport_id'];
}
if (isset($_GET['date']))
{
$date=$_GET['date'];
}
if (isset($_GET['cal']))
{
$cal=$_GET['cal'];
}
if (isset($_GET['dist']))
{
	$dist=$_GET['dist'];
}
if (isset($_GET['duration']))
{
	$duration=$_GET['duration'];
}
if (isset($_GET['fat']))
{
$fat=$_GET['fat'];
}
if (isset($_GET['above']))
{
$above=$_GET['above'];
}
if (isset($_GET['below']))
{
$below=$_GET['below'];
}
if (isset($_GET['in_zone']))
{
$in_zone=$_GET['in_zone'];
}
if (isset($_GET['lower']))
{
	$lower=$_GET['lower'];
}
if (isset($_GET['upper']))
{
	$upper=$_GET['upper'];
}
if (isset($_GET['fmoy']))
{
$fmoy=$_GET['fmoy'];
}
if (isset($_GET['fmax']))
{
$fmax=$_GET['fmax'];
}
if (isset($_GET['vmoy']))
{
$vmoy=$_GET['vmoy'];
}
if (isset($_GET['vmax']))
{
$vmax=$_GET['vmax'];
}
if (isset($_GET['altitude']))
{
	$altitude=$_GET['altitude'];
}
if (isset($_GET['url']))
{
	$url =  $_GET['url'];

}
if (isset($_GET['action']))
{
	$action=$_GET['action'];
}


if ($fmoy &&  $fmax &&  $upper &&  $lower &&  $in_zone &&  $below &&  $above  &&  $duration &&    $cal &&  $date &&  $sport_id &&  $seance_name && $action)
{
   
/* Connecting, selecting database */
    $link=connect_db($db_host, $db_username, $db_password, $db_name);
    print 'Acces à la base [<FONT COLOR=GREEN>OK</FONT>]<BR>';
//$date_parse=  date_create_from_format('j-m-Y', $date);
//echo "date($date_parse[year],\"y\")/$date_parse[month]/$date_parse[day]";
    if ($action == 'insert' )
    {
mysql_query("INSERT INTO seances  (`name`, `sport_id`, `date`, `calories`, `distance`, `duration`, `fat_consumption`, `above`, 
   `average`, `below`, `in_zone`, `lower`, `maximum`, `upper`, `Vaverage`, `Vmaximum`,`altitude`, `link` ) 
   values('$seance_name', '$sport_id', '$date'  , '$cal' , '$dist' , '$duration' , '$fat' , '$above' ,
    '$fmoy' ,  '$below' , '$in_zone', '$lower', '$fmax' , '$upper' , '$vmoy', '$vmax' , '$altitude' , '$url' )");

    echo "<pre>INSERT INTO seances  (`name`, `sport_id`, `date`, `calories`, `distance`, `duration`, `fat_consumption`, `above`,
   `average`, `below`, `in_zone`, `lower`, `maximum`, `upper`, `Vaverage`, `Vmaximum`,`altitude`, `link`)
   values('$seance_name', '$sport_id', '$date'  , '$cal' , '$dist' , '$duration' , '$fat' , '$above' ,
   '$fmoy' ,  '$below' , '$in_zone', '$lower', '$fmax' , '$upper' , '$vmoy', '$vmax', $altitude , '$url' )</pre>" ;
 
    }
    elseif ($action == 'update' && isset($seance_id) ) {
    mysql_query("update seances   set name ='$seance_name' , sport_id ='$sport_id' , date = '$date' , calories = '$cal',  distance = '$dist' , 
     duration ='$duration' , fat_consumption ='$fat' , above = '$above', average  =  '$fmoy', below = '$below' , in_zone = '$in_zone', 
     lower = '$lower', maximum = '$fmax', upper = '$upper', Vaverage = '$vmoy', Vmaximum = '$vmax' , altitude = '$altitude' , link = '$url'
     WHERE seance_id='$seance_id' ");	
     
	print  "<pre>update seances   set name ='$seance_name' , sport_id ='$sport_id' , date = '$date' , calories = '$cal',  distance = '$dist' , 
     duration ='$duration' , fat_consumption ='$fat' , above = '$above', average  =  '$fmoy', below = '$below' , in_zone = '$in_zone', 
     lower = '$lower', maximum = '$fmax', upper = '$upper', Vaverage = '$vmoy', Vmaximum = '$vmax' , altitude = '$altitude' , link =  '$url'
     WHERE seance_id = '$seance_id' </pre>" ;
    }
    elseif ( $action == 'delete'){
	mysql_query("delete from  seances  WHERE seance_id='$seance_id' ");	
	echo "delete from  seances  WHERE seance_id = '$seance_id' ";
	}
	mysql_close($link);
}
else
{
print("<H2>Tous les champs (sauf peut etre Place) doivent etre non vide!!!</H2>");
}
?>
</body>
</html>

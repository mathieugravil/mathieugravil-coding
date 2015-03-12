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


if (isset($_POST['seance_id']))
{
	$seance_id=$_POST['seance_id'];
}
if (isset($_POST['seance_name']))
{
$seance_name=$_POST['seance_name'];
}
if (isset($_POST['sport_id']))
{
$sport_id=$_POST['sport_id'];
}
if (isset($_POST['date']))
{
$date=$_POST['date'];
}
if (isset($_POST['cal']))
{
$cal=$_POST['cal'];
}
if (isset($_POST['dist']))
{
	$dist=$_POST['dist'];
}
if (isset($_POST['duration']))
{
	$duration=$_POST['duration'];
}
if (isset($_POST['fat']))
{
$fat=$_POST['fat'];
}
if (isset($_POST['above']))
{
$above=$_POST['above'];
}
if (isset($_POST['below']))
{
$below=$_POST['below'];
}
if (isset($_POST['in_zone']))
{
$in_zone=$_POST['in_zone'];
}
if (isset($_POST['lower']))
{
	$lower=$_POST['lower'];
}
if (isset($_POST['upper']))
{
	$upper=$_POST['upper'];
}
if (isset($_POST['fmoy']))
{
$fmoy=$_POST['fmoy'];
}
if (isset($_POST['fmax']))
{
$fmax=$_POST['fmax'];
}
if (isset($_POST['vmoy']))
{
$vmoy=$_POST['vmoy'];
}
if (isset($_POST['vmax']))
{
$vmax=$_POST['vmax'];
}
if (isset($_POST['altitude']))
{
	$altitude=$_POST['altitude'];
}
if (isset($_POST['url']))
{
	$url =  $_POST['url'];

}
if (isset($_POST['action']))
{
	$action=$_POST['action'];
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

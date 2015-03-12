<html>
  <head>
   <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
       <title></title>
  </head>

  <body >
</body>
<?php
$faire=1;

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


define('PUN_ROOT', './');
require PUN_ROOT.'config2.php';
require PUN_ROOT.'include/fonctions.php';

echo "INSERT INTO seances  (`name`, `sport_id`, `date`, `calories`, `distance`, `duration`, `fat_consumption`, `above`, `average`, `below`, `in_zone`, `lower`, `maximum`, `upper`, `Vaverage`, `Vmaximum`) values('$seance_name', '$sport_id', '$date'  , '$cal' , '$dist' , '$duration' , '$fat' , '$above' , '$fmoy' ,  '$below' , '$in_zone', '$lower', '$fmax' , '$upper' , '$vmoy', '$vmax' )" ;
if ($fmoy &&  $fmax &&  $upper &&  $lower &&  $in_zone &&  $below &&  $above  &&  $duration &&    $cal &&  $date &&  $sport_id &&  $seance_name)
{
   
/* Connecting, selecting database */
    $link=connect_db($db_host, $db_username, $db_password, $db_name);
    print 'Accès à la base [<FONT COLOR=GREEN>OK</FONT>]<BR>';
//$date_parse=  date_create_from_format('j-m-Y', $date);
//echo "date($date_parse[year],\"y\")/$date_parse[month]/$date_parse[day]";

   mysql_query("INSERT INTO seances  (`name`, `sport_id`, `date`, `calories`, `distance`, `duration`, `fat_consumption`, `above`, `average`, `below`, `in_zone`, `lower`, `maximum`, `upper`, `Vaverage`, `Vmaximum`) values('$seance_name', '$sport_id', '$date'  , '$cal' , '$dist' , '$duration' , '$fat' , '$above' , '$fmoy' ,  '$below' , '$in_zone', '$lower', '$fmax' , '$upper' , '$vmoy', '$vmax' )");

}
else
{
print("<H2>Tous les champs (sauf peut être Place) doivent être non vide!!!</H2>");
}
?>

<HTML>
   <HEAD>
         <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<TITLE>Statistique</TITLE>
          <link rel="stylesheet" href="style.css" type="text/css">

   </HEAD>
 <style type="text/css" src=style.css></style>
<script type="text/javascript" src=include/calendar.js>	</script>
	   	
 

<?php
define('PUN_ROOT', './');
require PUN_ROOT.'config2.php';
require PUN_ROOT.'include/fonctions.php';
require PUN_ROOT.'include/ExportExcel.php';

$start='1981/06/14';
$end=date("Y/m/d");

if (isset($_POST['start']))
{
	if ($_POST['start'] != '')
	{
		$start=$_POST['start'];
	}
	else{
		$start='1981/06/14';
		// $start=date('Y/m/d', mktime(0,0,0,date('m'),01,date('Y')));
	}
}

if (isset($_POST['end']))
{
	if ($_POST['end'] != '')
	{
		$end=$_POST['end'];
	}
	else{
		$end=date("Y/m/d");
	}
}
echo "<H1> du $start au  $end </H1>" ;
$link=connect_db($db_host, $db_username, $db_password, $db_name);
$query_sports = "select sport_id , sport_name from sport_type order by sport_id desc; ";
$sports = array();	
$result_sports = mysql_query($query_sports) or die("La requete $query_sports a echouee");
if(isset($_POST['sport']) && !empty($_POST['sport'])){
	$sports=$_POST['sport'];
	$all_selected=0;
}
else  {
	while ($row_sport=mysql_fetch_array($result_sports, MYSQL_NUM) )
	{
		$sports[] = $row_sport[0];
	}
	print "Vous devez selectionnez au moins un sport !!!!<br>";
$all_selected = 1;
}


$list_sports = "'". implode("', '", $sports) ."'";

$query="select name, sport_name, date, calories, distance, TIME_TO_SEC(duration), fat_consumption, TIME_TO_SEC(above), average, TIME_TO_SEC(below), TIME_TO_SEC(in_zone), lower, maximum, upper, Vaverage, Vmaximum 
from seances, sport_type 
Where seances.sport_id = sport_type.sport_id
AND seances.date <= date_format('$end','%Y/%m/%d')
AND seances.date >= date_format('$start','%Y/%m/%d')
AND seances.sport_id IN ($list_sports );";

$result = mysql_query($query) or die("La requete  $query a echouee");

while ($row = mysql_fetch_array($result, MYSQL_NUM))
{

echo"$row[0];$row[1];$row[2];$row[3];$row[4];$row[5];$row[6];$row[7];$row[8];$row[9];$row[10];$row[11];$row[12];$row[13];$row[14];$row[15]; \n <br>" ;

}

mysql_free_result($result);
mysql_close($link);
?>

	   </BODY>
 </HTML>



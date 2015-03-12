<HTML>
   <HEAD>
         <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<TITLE>Liste seances</TITLE>
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

$query="select seance_id, name, sport_name, date, calories, distance, duration, 
		fat_consumption, above, average, below, in_zone, lower, maximum, 
		upper, Vaverage, Vmaximum , altitude
from seances, sport_type 
Where seances.sport_id = sport_type.sport_id
AND seances.date <= date_format('$end','%Y/%m/%d')
AND seances.date >= date_format('$start','%Y/%m/%d')
AND seances.sport_id IN ($list_sports )
order by date desc;";


echo "<H1>Liste sur la periode du $start au  $end </H1>" ;

print"<form action=\"Liste.php\" method=\"post\">";
printf("<table border=2>\n
<TR>\n
<TD> First day </TD>\n<TD><Input name=\"start\" type=\"date\"   value=\"%s\" size=\"8\"/> </TD>\n<TD> Last day </TD>\n
 		<TD> <Input name=\"end\" type=\"date\"  value=\"%s\" size=\"8\"/></TD>\n 
</TR>\n",$start ,$end);
print "<TR><TD>Selectione un ou plusieurs sports : </TD><TD><select name=\"sport[]\" multiple>";

$result_sports = mysql_query($query_sports) or die("La requete $query_sports a echouee");

while ($row_sports = mysql_fetch_array($result_sports, MYSQL_NUM))
{
	if ($all_selected == 0)
	{
	if (  in_array($row_sports[0], $sports) )
	{
 printf("<option  value=%s selected>%s </option>",$row_sports[0],  $row_sports[1] );
	}
	else 
	{
		printf(	"<option  value=%s> %s </option>",$row_sports[0], $row_sports[1] );
	}
	}
	else 
	{
		printf("<option  value=%s selected> %s </option>",  $row_sports[0], $row_sports[1] );
	}
}	
print "</select></TD>";	
print "	<TD><INPUT TYPE=\"SUBMIT\" VALUE=\"Report\"/></form></TD><TD><form action=\"Export.php\" method=\"post\">
<input type=\"hidden\" name=\"start\" value=\"$start\">
<input type=\"hidden\" name=\"end\" value=\"$end\">";

$result_sports = mysql_query($query_sports) or die("La requete $query_sports a echouee");
while ($row_sports = mysql_fetch_array($result_sports, MYSQL_NUM))
{
	if ($all_selected == 0)
	{
	if (  in_array($row_sports[0], $sports) )
	{
 printf("<input type=\"hidden\" name=\"sport[]\" value=\"%s\" >",$row_sports[0]);
	}	
	}
	else 
	{
		printf("<input type=\"hidden\" name=\"sport[]\" value=\"%s\" >",$row_sports[0]);
	}
}	

print"
<INPUT TYPE=\"SUBMIT\" VALUE=\"CSV_export\"/></form>
</TD>
</TR>	
</table>\n";

$querysum1 = "select sport_name, SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps passe\" ,
sum(calories) as \"Calories depensees\" ,
sum(distance) /1000 as \"distance(km)\" ,
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)) , 2) as \"Calorie/heure\",
count(distinct(date)) as \"nb days\" , 
format(sum(calories)/count(distinct(date)),2) as \"Cal/day\",
format(sum(distance) /1000/count(distinct(date)),2) as \"km/day\", 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/count(distinct(date)))as \"duration/day\"
 
FROM seances, sport_type
WHERE seances.sport_id=sport_type.sport_id
AND seances.date <= date_format('$end','%Y/%m/%d')
AND seances.date >= date_format('$start','%Y/%m/%d')
AND seances.sport_id IN ($list_sports )
GROUP BY sport_name
;";

$header[0]="Sport name";
$header[1]="Duree (HH:MM:ss)";
$header[2]="Calories (cal)";
$header[3]="Distance (km)";
$header[4]="Calorie/heure";
$header[5]= "Nb jours act";
$header[6]="Calorie/ jour act"; 
$header[7]="Distance par jour act (km/j)";
$header[8]="Duree/jour act ";

mysql_to_html_table($link, $querysum1, $header) ;


$querysum2="SELECT count(distinct(date)) as \"nb days\" ,
format(sum(calories)/count(distinct(date)),2) as \"Cal/day\",
format(sum(distance) /1000/count(distinct(date)),2) as \"km/day\",
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/count(distinct(date)))as \"duration/day\",
datediff(max(date), min(date)) as \"nb days eff\" ,
format(sum(calories)/datediff(max(date), min(date)),2) as \"Cal/dayeff\",
format(sum(distance) /1000/datediff(max(date), min(date)),2) as \"km/dayeff\",
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/datediff(max(date), min(date)))as \"duration/dayeff\"
from seances
WHERE seances.date <= date_format('$end','%Y/%m/%d')
AND seances.date >= date_format('$start','%Y/%m/%d')
AND seances.sport_id IN ($list_sports )
;";

$header2[0]="NB jours act periode"; //
$header2[1]="Calorie/ jour act"; //
$header2[2]="Distance par jour act";//Nb jour periode";
$header2[3]="Duree/jour act";//Calorie/heure";
$header2[4]="NB jours  periode";
$header2[5]="Calorie/ jour"; //
$header2[6]="Distance par jour (km/j)";//Nb jour periode";
$header2[7]="Duree/jour";//Calorie/heure"; 
mysql_to_html_table($link, $querysum2, $header2) ;


$querysum3="select date_format(date, '%M %Y'), 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps passe\" ,
sum(calories) as \"Calories depensees\" , 
sum(distance) /1000 as \"distance(km)\" ,
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)) , 2) as \"Calorie/heure\" ,
count(distinct(date)) as \"nb days\" , 
format(sum(calories)/count(distinct(date)),2) as \"Cal/day\",
format(sum(distance) /1000/count(distinct(date)),2) as \"km/day\", 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/count(distinct(date)))as \"duration/day\",  
		format(sum(calories)/datediff(max(date), min(date)),2) as \"Cal/dayeff\", 
		format(sum(distance) /1000/datediff(max(date), min(date)),2) as \"km/dayeff\", 
		SEC_TO_TIME(sum(TIME_TO_SEC(duration))/datediff(max(date), min(date)))as \"duration/dayeff\"
		FROM seances, sport_type 
		WHERE seances.sport_id=sport_type.sport_id 
		AND seances.date <= date_format('$end','%Y/%m/%d')
AND seances.date >= date_format('$start','%Y/%m/%d')
		 AND seances.sport_id IN ($list_sports )
GROUP BY  date_format(date, '%M %Y')
ORDER BY date ;";
$header3[0]="Mois"; 
$header3[1]="Duree"; 
$header3[2]="Calorie";
$header3[3]="Distance";
$header3[4]="Calorie/h";
$header3[5]= "Nb jours act";
$header3[6]="Calorie/ jour act"; 
$header3[7]="Distance par jour act (km/j)";
$header3[8]="Duree/jour act ";
$header3[9]="Calorie/ jour";
$header3[10]="Distance par jour (km/j)";
$header3[11]="Duree/jour";
mysql_to_html_table($link, $querysum3, $header3) ;





print "<TABLE border=2><TR>
<TD>name</TD><TD>sport</TD><TD>date</TD><TD>cal</TD><TD>dist</TD><TD>duration</TD><TD> 
		%fat</TD><TD>above</TD><TD>average</TD><TD>below</TD><TD>in_zone</TD><TD> 
		lower</TD><TD>maximum</TD><TD>upper</TD><TD>Vaverage</TD><TD>Vmaximum </TD><TD>altitude</TD>
		<TD>&nbsp</TD>
		</TR>";

$result = mysql_query($query) or die("La requete  $query a echouee");
$num_rows = mysql_num_rows($result);
echo "$num_rows Rows\n";
		

while ($row = mysql_fetch_array($result, MYSQL_NUM))
{
print"<form action=\"Voir.php\" method=\"post\">";
printf("<input type=\"hidden\" name=\"seance_id\" value=\"%s\">",$row[0]);
echo"<TR>
<TD>$row[1]</TD><TD>$row[2]</TD><TD>$row[3]</TD><TD>$row[4]</TD><TD>$row[5]</TD><TD>$row[6]</TD><TD>
$row[7]</TD><TD>$row[8]</TD><TD>$row[9]</TD><TD>$row[10]</TD><TD>$row[11]</TD><TD>$row[12]</TD><TD>$row[13]</TD><TD>
$row[14]</TD><TD>$row[15]</TD><TD>$row[16]</TD><TD>$row[17]</td><TD><INPUT TYPE=\"SUBMIT\" VALUE=\"Voir\"/></TD>

</TR>" ;
print"</form>";
}
print "</TABLE>";

mysql_free_result($result);
mysql_free_result($result_sports);
mysql_close($link);
?>

	   </BODY>
 </HTML>


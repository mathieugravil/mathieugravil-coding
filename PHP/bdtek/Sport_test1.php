<HTML>
   <HEAD>
         <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	
          <link rel="stylesheet" href="style.css" type="text/css">

   </HEAD>
   <body bgcolor='#f4d7b7'>
	   <H1>Stat</H1>	
 

<?php

define('PUN_ROOT', './');
require PUN_ROOT.'config2.php';

require PUN_ROOT.'include/fonctions.php';
include "../libchart/libchart/classes/libchart.php";

// Width of the chart
$width = 200;

$chart_cal_h_sp = new HorizontalBarChart(500, 300);
$dataSet_cal_h_sp = new XYDataSet();

echo "db_host: $db_host";
echo "db_username: $db_username";
echo "db_name: $db_name";
echo 'Version PHP courante : ' . phpversion();

   /* Connecting, selecting database */
 
$link=connect_db($db_host, $db_username, $db_password, $db_name);
	
	
	$query = "select sport_name, SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps passé\" , 
sum(calories) as \"Calories dépensées\" ,
sum(distance) /1000 as \"distance(km)\" , 
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)) , 2) as \"Calorie/heure\"
FROM mathieugravil_zzl_sports.seances, mathieugravil_zzl_sports.sport_type
WHERE seances.sport_id=sport_type.sport_id
GROUP BY sport_name
;";
/*
UNION
SELECT \"TOTAL\", SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps passé\" , sum(calories) as \"Calories dépensées\" , 
sum(distance) /1000 as \"distance(km)\", 
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)),2) as \"Calorie/heure\"
FROM mathieugravil_zzl_sports.seances, mathieugravil_zzl_sports.sport_type
WHERE seances.sport_id=sport_type.sport_id;";
*/
     $result = mysql_query($query) or die("La requête a echouée");
	
  /* Printing results in HTML */
	
$i=0;
      print "<table border=2>\n";
print"<TR>
<TD>Sport</TD>
<TD>temps passé</TD>
<TD>Calories dépensées</TD>
<TD>distance(km)</TD>
<TD>Calorie/heure</TD>
</TR>";
      while ($row = mysql_fetch_array($result, MYSQL_NUM))
         {
		
	  printf("<TR> 
<TD> %s </TD> 
<TD>%s </TD> 
<TD>%s </TD> 
<TD>%s </TD> 
<TD>%s </TD>
</TR>",  $row[0] , $row[1] ,  $row[2], $row[3], $row[4] );
 
if ($i>0)
         	{
         	$labelt=$labelt."*";
         	$datat=$datat."*";
		$labelc=$labelc."*";
         	$datac=$datac."*";
		$labeld=$labeld."*";
         	$datad=$datad."*";
         	}
         //	printf("<TR><TD>%s</TD><TD>%s</TD></TR>", $row[0],$row[1] );
         	$labelt=$labelt.$row[0];
         	$datat=$datat.$row[1];
		$labelc=$labelc.$row[0];
         	$datac=$datac.$row[2];
		$labeld=$labeld.$row[0];
         	$datad=$datad.$row[3];

		$dataSet_cal_h_sp->addPoint(new Point($row[0], $row[4]));

         	$i++;	  



	   $nb=$nb+1;  
	 }
	 print "</table>\n";
$chart_cal_h_sp->setDataSet($dataSet_cal_h_sp);
	$chart_cal_h_sp->setTitle("Calories/h");
	$chart_cal_h_sp->render("generated/cal_h_sp.png");
 /* Free resultset */
 mysql_free_result($result);
 /* Closing connection */
  mysql_close($link);

	
echo"
<table>
<tr><td> Sport par temps passé </td><td> Sport par calories dépensées</td></tr>
<tr>
<td>
";
printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labelt, $datat);
echo" </td>
<td>";
printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labelc, $datac);
echo "</td>
</tr>
<tr> <td> Sport par distance parcourue </td><td> Calories /h par sport </td>
</tr>
<tr>
<td>";

printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labeld, $datad);
echo "</td>
<td>";
printf("<img src=\"generated/cal_h_sp.png\"></img>");
echo "
</tr>
</table>";

?>

No default date
Set year navigate from 2000 to 2015
Allow date selectable from 13 May 2008 to 01 March 2015
Allow to navigate other dates from above
Date input box set to false
Set alignment left and bottom
Disable specific date 1, 4 April, and 25 December of every years
Disable specific date 10, 14 of every months
Disable 1 June 2011
Code:

<?php
	  $myCalendar = new tc_calendar("date5", true, false);
	  $myCalendar->setIcon("calendar/images/iconCalendar.gif");
	  $myCalendar->setDate(date('d'), date('m'), date('Y'));
	  $myCalendar->setPath("calendar/");
	  $myCalendar->setYearInterval(2000, 2015);
	  $myCalendar->dateAllow('2008-05-13', '2015-03-01');
	  $myCalendar->setDateFormat('j F Y');
	  $myCalendar->setAlignment('left', 'bottom');
	  $myCalendar->setSpecificDate(array("2011-04-01", "2011-04-04", "2011-12-25"), 0, 'year');
	  $myCalendar->setSpecificDate(array("2011-04-10", "2011-04-14"), 0, 'month');
	  $myCalendar->setSpecificDate(array("2011-06-01"), 0, '');
	  $myCalendar->writeScript();
	  ?>

	   </BODY>
 </HTML>

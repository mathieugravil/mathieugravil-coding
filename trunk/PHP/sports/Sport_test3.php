<HTML>
   <HEAD>
         <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	
          <link rel="stylesheet" href="style.css" type="text/css">

   </HEAD>
 <style type="text/css" src=style.css>

</style>
<script type="text/javascript" src=include/calendar.js>

	</script>
	   	
 

<?php
define('PUN_ROOT', './');
require PUN_ROOT.'config2.php';
require PUN_ROOT.'include/fonctions.php';
include "../libchart/libchart/classes/libchart.php";
include("../lib/pChart/pData.class");
 include("../lib/pChart/pChart.class");
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


echo "<H1>Statistique sur la p�riode du $start au  $end </H1>" ;

// Width of the chart
$width = 200;
$chart_cal_h_sp = new HorizontalBarChart(500, 300);
$dataSet_cal_h_sp = new XYDataSet();




/*
echo "db_host: $db_host";
echo "db_username: $db_username";
echo "db_name: $db_name";
echo 'Version PHP courante : ' . phpversion();
*/
   /* Connecting, selecting database */
 
$link=connect_db($db_host, $db_username, $db_password, $db_name);
	
	
	$query = "select sport_name, SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps pass�\" , 
sum(calories) as \"Calories d�pens�es\" ,
sum(distance) /1000 as \"distance(km)\" , 
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)) , 2) as \"Calorie/heure\"
FROM seances, sport_type
WHERE seances.sport_id=sport_type.sport_id
AND date <= '$end'
AND date >= '$start'
GROUP BY sport_name
;";
$result = mysql_query($query) or die("La requ�te a echou�e");


	
$query2 = "select  date_format(date,'%d/%m/%y' ),
sum(TIME_TO_SEC(below)) as \"Temps en dessous\", 
sum(TIME_TO_SEC(in_zone)) as \"Temps dans la zone\", 
sum(TIME_TO_SEC(above)) as \"Temps au dessus\"
FROM seances, sport_type
WHERE seances.sport_id=sport_type.sport_id
AND date <= '$end'
AND date >= '$start'
GROUP BY date;
;";
     $result2 = mysql_query($query2) or die("La requ�te a echou�e");

$query3 = "SELECT  SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps pass�\" ,
 sum(calories) as \"Calories d�pens�es\" , 
sum(distance) /1000 as \"distance(km)\", 
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)),2) as \"Calorie/heure\" ,
 min(date) as \"Start\", max(date) as \"End\" , count(distinct(date)) as \"nb days\" ,
format(sum(calories)/count(distinct(date)),2) as \"Cal/day\",
format(sum(distance) /1000/count(distinct(date)),2) as \"km/day\", 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/count(distinct(date)))as \"duration/day\",
datediff(max(date), min(date)) as \"nb days eff\" ,
format(sum(calories)/datediff(max(date), min(date)),2) as \"Cal/dayeff\",
format(sum(distance) /1000/datediff(max(date), min(date)),2) as \"km/dayeff\", 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/datediff(max(date), min(date)))as \"duration/dayeff\"
from seances
WHERE date <= '$end'
AND date >= '$start'
;";
 $result3 = mysql_query($query3) or die("La requ�te a echou�e");    

$query4="select  
SEC_TO_TIME(sum(TIME_TO_SEC(below))) as \"Temps en dessous\", 
SEC_TO_TIME(sum(TIME_TO_SEC(in_zone))) as \"Temps dans la zone\", 
SEC_TO_TIME(sum(TIME_TO_SEC(above))) as \"Temps au dessus\"
FROM seances
WHERE date <= '$end'
AND date >= '$start';";
$result4 = mysql_query($query4) or die("La requ�te 4 a echou�e");


$query5="select sport_name,
sum(TIME_TO_SEC(below)) as \"Temps en dessous\", 
sum(TIME_TO_SEC(in_zone)) as \"Temps dans la zone\", 
sum(TIME_TO_SEC(above)) as \"Temps au dessus\"
FROM seances, sport_type
WHERE seances.sport_id=sport_type.sport_id
AND date <= '$end'
AND date >= '$start'
GROUP BY sport_name 
order by \"Temps en dessous\",  \"Temps dans la zone\", \"Temps au dessus\" desc;";
$result5 = mysql_query($query5) or die("La requ�te 5 a echou�e");

$query6="
SELECT A.date, format(sum(A.product)/B.total_duration,0) as barycenter, B.max
from
(SELECT date, average*TIME_TO_SEC(duration) as product
from seances) A, 
(SELECT date, sum(TIME_TO_SEC(duration)) as total_duration, max(maximum) as max
from seances group by date) B
WHERE 
A.date = B.date
AND A.date <= '$end'
AND A.date >= '$start'
group by date 
order by date
;";
$result6 = mysql_query($query6) or die("La requ�te 6 a echou�e");

$query70="
select distinct(date)  
FROM seances
WHERE date <= '$end'
AND date >= '$start'
ORDER BY  date;
";
$result70 = mysql_query($query70) or die("La requ�te 70 a echou�e");

$query71="select distinct(seances.sport_id), sport_name  FROM seances,  sport_type WHERE seances.sport_id=sport_type.sport_id
and Distance != 0 
AND date <= '$end'
AND date >= '$start';";
$result71 = mysql_query($query71) or die("La requ�te 71 a echou�e");




//=====================Begin result 4==================================================

$row4 = mysql_fetch_array($result4, MYSQL_NUM);
         	$labelsz=$labelsz."Time below";
         	$datasz=$datasz.$row4[0];
		$labelsz=$labelsz."*";
         	$datasz=$datasz."*";
		$labelsz=$labelsz."Time in zone";
         	$datasz=$datasz.$row4[1];
		$labelsz=$labelsz."*";
		$datasz=$datasz."*";
		$labelsz=$labelsz."Time above";
         	$datasz=$datasz.$row4[2];

//=====================End result 4==================================================


//=====================Begin result 2==================================================
$DataSet = new pData;

while ($row2 = mysql_fetch_array($result2, MYSQL_NUM))
         {
$DataSet->AddPoint($row2[0],"abscisse");
$DataSet->AddPoint($row2[1],"below");
 $DataSet->AddPoint($row2[2],"in_zone");
 $DataSet->AddPoint($row2[3],"above");
}

$DataSet->AddSerie("below");
$DataSet->AddSerie("in_zone");
$DataSet->AddSerie("above");

 $DataSet->SetAbsciseLabelSerie("abscisse");
 $DataSet->SetSerieName("Temps en dessous","below");
 $DataSet->SetSerieName("Temps dans la zone","in_zone");
 $DataSet->SetSerieName("Temps au dessus","above");
$DataSet->SetYAxisFormat("time"); 

 // Initialise the graph
 $Test = new pChart(1000,450);
 $Test->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test->setGraphArea(80,10,1000,340);
 //$Test->drawRoundedRectangle(0,0,1000,450,5,255,255,255);
 //$Test->drawRoundedRectangle(0,0,1000,350,5,260,260,260);
 $Test->drawGraphArea(255,255,255,TRUE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALL,150,150,150,TRUE,90,2,TRUE);
 $Test->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the bar graph
 $Test->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),100);

 // Finish the graph
 $Test->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test->drawLegend(0,0,$DataSet->GetDataDescription(),255,255,255);
 $Test->setFontProperties("../lib/Fonts/tahoma.ttf",10);
 $Test->drawTitle(50,22,"Evolution dans le temps de la r�partition du temps dans chaque zone",50,50,50,585);


 $Test->Render("generated/example20.png");
//===========End Result 2==========================
//========================Begin result 5===============================================
$DataSet5 = new pData;
while ($row5 = mysql_fetch_array($result5, MYSQL_NUM))
         {
$DataSet5->AddPoint($row5[0],"sport");
$DataSet5->AddPoint($row5[1],"below");
 $DataSet5->AddPoint($row5[2],"in_zone");
 $DataSet5->AddPoint($row5[3],"above");
//echo " $row5[0]\t$row5[1]\t$row5[2]\t$row5[3]\n";
}


$DataSet5->AddSerie("below");
$DataSet5->AddSerie("in_zone");
$DataSet5->AddSerie("above");

 $DataSet5->SetAbsciseLabelSerie("sport");
 $DataSet5->SetSerieName("Temps en dessous","below");
 $DataSet5->SetSerieName("Temps dans la zone","in_zone");
 $DataSet5->SetSerieName("Temps au dessus","above");
$DataSet5->SetYAxisFormat("time"); 

 // Initialise the graph
 $Test5 = new pChart(700,330);

 $Test5->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test5->setGraphArea(60,10,680,270);
 $Test5->drawRoundedRectangle(7,7,693,293,5,240,240,240);
 $Test5->drawRoundedRectangle(5,5,695,295,5,230,230,230);
 $Test5->drawGraphArea(255,255,255,TRUE);
 $Test5->drawScale($DataSet5->GetData(),$DataSet5->GetDataDescription(),SCALE_ADDALL,150,150,150,TRUE,0,2,TRUE);
 $Test5->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test5->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test5->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the bar graph
 $Test5->drawStackedBarGraph($DataSet5->GetData(),$DataSet5->GetDataDescription(),TRUE);

 // Finish the graph
 $Test5->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test5->drawLegend(0,0,$DataSet5->GetDataDescription(),255,255,255);
 $Test5->setFontProperties("../lib/Fonts/tahoma.ttf",10);
 $Test5->drawTitle(50,22,"R�partition du temps dans chaque zone par sport",50,50,50,585);


 $Test5->Render("generated/sportzone_sport.png");
//=============End result 5========================

//====================Begin result 6=========================
$DataSet6 = new pData;
while ($row6 = mysql_fetch_array($result6, MYSQL_NUM))
         {
$DataSet6->AddPoint($row6[0],"date");
$DataSet6->AddPoint($row6[1],"Fmoy");
$DataSet6->AddPoint($row6[2],"Fmax");
//echo " $row6[0]\t$row6[1]\t$row6[2]\n";
}
$DataSet6->AddSerie("Fmoy");
$DataSet6->AddSerie("Fmax");
$DataSet6->SetAbsciseLabelSerie("date");
 $DataSet6->SetSerieName("FC Moy","Fmoy");
 $DataSet6->SetSerieName("FC Max","Fmax");

 // Initialise the graph
$Test6 = new pChart(1000,330);
 $Test6->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test6->setGraphArea(60,10,980,260);
 //$Test6->drawRoundedRectangle(7,7,993,293,5,240,240,240);
 //$Test6->drawRoundedRectangle(5,5,995,295,5,230,230,230);
 $Test6->drawGraphArea(255,255,255,TRUE);
 $Test6->drawScale($DataSet6->GetData(),$DataSet6->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,90,2,TRUE);
 $Test6->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test6->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test6->drawTreshold(0,143,55,72,TRUE,TRUE);
// Draw the line graph  
 $Test6->drawLineGraph($DataSet6->GetData(),$DataSet6->GetDataDescription());     
 $Test6->drawPlotGraph($DataSet6->GetData(),$DataSet6->GetDataDescription(),3,2,255,255,255);     
   
 // Finish the graph     
 $Test6->setFontProperties("../lib/Fonts/tahoma.ttf",8);     
 $Test6->drawLegend(0,0,$DataSet6->GetDataDescription(),255,255,255);     
 $Test6->setFontProperties("../lib/Fonts/tahoma.ttf",10);     
 $Test6->drawTitle(60,22,"Evolution des Fr�quence cardiaque",50,50,50,585);     
 $Test6->Render("generated/FC.png");        
     
//============================End result 6=========================================
//====================Begin result 7=========================
$DataSet7 = new pData;
$DataSet8 = new pData;
while ($row70 = mysql_fetch_array($result70, MYSQL_NUM))
         {
	$DataSet7->AddPoint($row70[0],"date");
	$DataSet8->AddPoint($row70[0],"date");
/*	echo " <br>$row7[0]\t $row7[1]\t$row7[2]\t$row6[3]\n";
	$DataSet7->AddPoint($row7[2],$row71[0]." Vmoy");
	$DataSet7->AddPoint($row7[3],$row71[0]." Vmax");
*/
	$result71 = mysql_query($query71) or die("La requ�te 71 a echou�e");
	while ($row71 = mysql_fetch_array($result71, MYSQL_NUM))
	        {
	//		echo "<br> $row7[0]\t $row7[1] != $row71[0] \t 0 \t 0 \n";
		$query7="select  date,sport_name,format(avg(Vaverage),2) as \"Vitesse moyenne\",
 			format(max(Vmaximum),2) as \"Vitesse max\", 
			format(sum(distance )/1000, 2) as \"Distance\" 
			FROM seances, sport_type
			WHERE seances.sport_id=sport_type.sport_id
			and Distance != 0
			and date = '$row70[0]'
			and sport_type.sport_id ='$row71[0]' 
			GROUP BY  date, sport_name;";
			$result7 = mysql_query($query7) or die("La requ�te $query7 a echou�e");
			if (mysql_num_rows($result7)== 1 )
			{
				while ($row7 = mysql_fetch_array($result7, MYSQL_NUM))
				{
				$DataSet7->AddPoint($row7[2],$row7[1]." Vmoy");
				$DataSet7->AddPoint($row7[3],$row7[1]." Vmax");
				$DataSet8->AddPoint($row7[4],$row7[1]." Distance(km)");
			//	echo "<br> $row70[0]\t $row7[1]  \t $row7[2] \t $row7[3] \n";
				}
			}else{
			$DataSet7->AddPoint( 0 , $row71[1]." Vmoy");
			$DataSet7->AddPoint(0 , $row71[1]." Vmax");
			$DataSet8->AddPoint( 0 ,$row71[1]." Distance(km)");
			//echo "<br> $row70[0]\t $row71[1]  \t 0 \t 0 \n";
			}
		}
	}
$result71 = mysql_query($query71) or die("La requ�te 71 a echou�e");
while ($row71 = mysql_fetch_array($result71, MYSQL_NUM))
         {
$DataSet7->AddSerie($row71[1]." Vmoy" );
$DataSet7->AddSerie($row71[1]." Vmax" );
$DataSet8->AddSerie($row71[1]." Distance(km)" );
// $DataSet7->SetSerieName("","Fmax");
}

$DataSet7->SetAbsciseLabelSerie("date");

 // Initialise the graph
$Test7 = new pChart(1000,330);
 $Test7->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test7->setGraphArea(60,10,980,260);
 //$Test7->drawRoundedRectangle(7,7,993,293,5,240,240,240);
 //$Test7->drawRoundedRectangle(5,5,995,295,5,230,230,230);
 $Test7->drawGraphArea(255,255,255,TRUE);
 $Test7->drawScale($DataSet7->GetData(),$DataSet7->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,90,2,TRUE);
 $Test7->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test7->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test7->drawTreshold(0,143,55,72,TRUE,TRUE);
// Draw the line graph  
 $Test7->drawLineGraph($DataSet7->GetData(),$DataSet7->GetDataDescription());     
 $Test7->drawPlotGraph($DataSet7->GetData(),$DataSet7->GetDataDescription(),3,2,255,255,255);     
   
 // Finish the graph     
 $Test7->setFontProperties("../lib/Fonts/tahoma.ttf",8);     
 $Test7->drawLegend(0,0,$DataSet7->GetDataDescription(),255,255,255);     
 $Test7->setFontProperties("../lib/Fonts/tahoma.ttf",10);     
 $Test7->drawTitle(60,10,"Vitesse",50,50,50,585);     
 $Test7->Render("generated/vitesse.png");        
  

//==== Begin graph 8 ======
$DataSet8->SetAbsciseLabelSerie("date");

 // Initialise the graph
$Test8 = new pChart(1000,330);
 $Test8->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test8->setGraphArea(60,10,980,260);
 //$Test7->drawRoundedRectangle(7,7,993,293,5,240,240,240);
 //$Test7->drawRoundedRectangle(5,5,995,295,5,230,230,230);
 $Test8->drawGraphArea(255,255,255,TRUE);
 $Test8->drawScale($DataSet8->GetData(),$DataSet8->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,90,2,TRUE);
 $Test8->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test8->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test8->drawTreshold(0,143,55,72,TRUE,TRUE);
// Draw the line graph  
 $Test8->drawLineGraph($DataSet8->GetData(),$DataSet8->GetDataDescription());     
 $Test8->drawPlotGraph($DataSet8->GetData(),$DataSet8->GetDataDescription(),3,2,255,255,255);     
   
 // Finish the graph     
 $Test8->setFontProperties("../lib/Fonts/tahoma.ttf",8);     
 $Test8->drawLegend(0,0,$DataSet8->GetDataDescription(),255,255,255);     
 $Test8->setFontProperties("../lib/Fonts/tahoma.ttf",10);     
 $Test8->drawTitle(60,10,"Distance (km)",50,50,50,585);     
 $Test8->Render("generated/distance.png");        
//====  End graph 8 ==== 
//============================End result 7=========================================

while ($row3 = mysql_fetch_array($result3, MYSQL_NUM))
         {
print"<form action=\"Sport_test2.php\" method=\"post\">";
printf("<table border=2>\n
<TR>\n
<TD> First day </TD>\n<TD><Input name=\"start\" type=\"date\"  onclick=\"new calendar(this);\" value=\"%s\" size=\"8\"/> </TD>\n<TD> Last day </TD>\n<TD> <Input name=\"end\" type=\"date\"  onclick=\"new calendar(this);\" value=\"%s\" size=\"8\"/></TD>\n <TD><INPUT TYPE=\"SUBMIT\" VALUE=\"Report\"/></TD>\n
</TR>\n
</table></form>\n",$row3[4] ,$row3[5]);

 /* Printing results in HTML */
	
$i=0;

print"<table border=2>\n";
print"<TR>
<TD>Sport</TD>
<TD>temps pass�</TD>
<TD>Calories d�pens�es</TD>
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

printf("<TR> 
<TD>TOTAL </TD> 
<TD>%s </TD> 
<TD>%s </TD> 
<TD>%s </TD> 
<TD>%s </TD>
</TR></table>\n",  $row3[0] , $row3[1] ,  $row3[2], $row3[3]);
 



printf("
<table border=2>\n
<TR>
<TD> Nb days </TD><TD> %s </TD><TD> Nb days of activity </TD><TD> %s </TD>
</TR>
<TR>
<TD> Cal/day </TD><TD> %s </TD><TD> Cal/day of activity </TD><TD> %s </TD>
</TR>
<TR>
<TD> km/day </TD><TD> %s </TD><TD> km/day of activity </TD><TD> %s </TD>
</TR>
<TR>
<TD> Duration/day </TD><TD> %s </TD><TD> Duration/day of activity </TD><TD> %s </TD>
</TR></table>\n
", $row3[10], $row3[6],  $row3[11] ,  $row3[7],$row3[12], $row3[8] , $row3[13], $row3[9]);

}



 

	 
$chart_cal_h_sp->setDataSet($dataSet_cal_h_sp);
	$chart_cal_h_sp->setTitle("Calories/h");
	$chart_cal_h_sp->render("generated/cal_h_sp.png");
 /* Free resultset */
 mysql_free_result($result);
 mysql_free_result($result2);
 mysql_free_result($result3);
 mysql_free_result($result4);
 mysql_free_result($result5);
 mysql_free_result($result6);
 mysql_free_result($result7);
 mysql_free_result($result71);
 /* Closing connection */
  mysql_close($link);

	


echo"
<table>
<tr><td> Sport par temps pass� </td><td> Sport par calories d�pens�es</td></tr>
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
</table>
<table>
<tr><td> Repartition in sport zone </td></tr>
<tr>
<td>
";
printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labelsz, $datasz);
echo" </td>
<td>";
printf("<img src=\"generated/sportzone_sport.png\"></img>");
echo "</td>
</tr>
<tr>
<td>
</td>
</tr>
</table>";
echo"
<table>
<tr>
<td>";
printf("<img src=\"generated/example20.png\"></img>");
echo "
</td>
</tr><tr><td>";
printf("<img src=\"generated/FC.png\"></img>");
echo"
</td></tr>
<tr>
<td>";
printf("<img src=\"generated/vitesse.png\"></img>");
echo"
</td></tr>
<tr>
<td>";
printf("<img src=\"generated/distance.png\"></img>");

echo"</td></tr></table>";

?>

	   </BODY>
 </HTML>

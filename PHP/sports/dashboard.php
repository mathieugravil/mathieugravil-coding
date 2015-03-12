<?php
define('PUN_ROOT', '../');
require PUN_ROOT.'/sports/config2.php';
require PUN_ROOT.'/rf/razorflow.php';
require PUN_ROOT.'/sports/include/fonctions.php';

$link=connect_db($db_host, $db_username, $db_password, $db_name);
$query_sports = "select sport_id , sport_name from sport_type order by sport_id desc; ";
$sports = array();	



$dataSource = new MySQLDataSource($db_name, $db_username ,  $db_password, $db_host);


$dataSource->setSQLSource('(SELECT date, sport_name, calories, 3600 * calories / TIME_TO_SEC( duration ) AS cal_H,  TIME_TO_SEC( duration ) AS duration, distance /1000 AS DIST_KM, fat_consumption,  TIME_TO_SEC( above ) AS above ,  average,  TIME_TO_SEC( below ) AS below , TIME_TO_SEC(  in_zone ) AS in_zone , lower, maximum, upper, Vaverage, Vmaximum, altitude
FROM seances, sport_type
WHERE seances.sport_id = sport_type.sport_id
AND sport_type.sport_id = seances.sport_id order by sport_name) AS sub_query');



$filter = new AutoFilterComponent();
$filter->setCaption("Sport");
$filter->setDataSource($dataSource);
$filter->addMultiSelectFilter("Sport", "sport_name");
$filter->addTimeRangeFilter("Date", "date");
Dashboard::addcomponent($filter);
$sports_cal= new ChartComponent();
$sports_cal->setCaption ("Sports par calories dépensées");
$sports_cal->setDataSource($dataSource);

$sports_cal->setLabelExpression("Sports", "sport_name");
$sports_cal->addSeries("Sports", "calories", array(
                'displayType' => "Pie",
		'sort' => "DESC"
));
$sports_cal->setOption('limit', 10);
Dashboard::addComponent($sports_cal);

$filter->addFilterTo($sports_cal);

###########################################
##############START ZONE ##################
###########################################
$TZ = new ChartComponent();


$TZ->setCaption("Time Zone");
$TZ->setWidth(4);
$TZ->setDataSource($dataSource);
$TZ->setYAxis("TIME", array(
		'adaptiveYMin' => true
));
#$TZ->setSecondYAxis("Duration", array(
#		'adaptiveYMin' => true
#));
$TZ->setLabelExpression("Date", "date", array(
		'timestampRange' => true,
               		'timeUnit' => 'month',
                'customTimeUnitPath' => array('month', 'day'),
                'autoDrill' => true

));
$TZ->addSeries("BELOW", "below  / 3600 ", array(   
'displayType' => "Line",    
		'decimals' => 0 ,
		'aggregateFunction' => "SUM"
                 
) );
$TZ->addSeries("IN_ZONE", "in_zone / 3600 ", array(
'displayType' => "Line",
		'decimals' => 0 ,
		'aggregateFunction' => "SUM"
) );
$TZ->addSeries("ABOVE", "above / 3600 ", array( 
'displayType' => "Line",           
		'decimals' => 0 ,
		'aggregateFunction' => "SUM"
));


$TZ->addSeries("Duration", "duration / 3600 ", array(
		'decimals' => 0 ,
		'aggregateFunction' => "SUM"
#,
#                'onSecondYAxis' => true
));

$TZ->setOption('showValues', false);

Dashboard::addComponent($TZ);




$filter->addFilterTo($TZ);
###########################################
##############END ZONE#####################
###########################################


###########################################
###########START FC########################
###########################################
$FC = new ChartComponent();
$FC->setCaption("FC");
$FC->setWidth(4);
$FC->setDataSource($dataSource);
$FC->setYAxis("BPM", array(
		'adaptiveYMin' => true
));
$FC->setLabelExpression("Date", "date", array(
		'timestampRange' => true,
               		'timeUnit' => 'month',
                'customTimeUnitPath' => array('month', 'day'),
                'autoDrill' => true

));
$FC->addSeries("FCmax", "maximum", array(
	         'displayType' => "Area",
		'decimals' => 0 ,
		'aggregateFunction' => "MAX"
));
$FC->addSeries("FCmoy", "average", array(
                'displayType' => "Area",
		'decimals' => 0 ,
		'aggregateFunction' => "AVG"
));
$FC->setOption('showValues', false);
Dashboard::addComponent($FC);
$filter->addFilterTo($FC);
###########################################
############END  FC########################
###########################################

###########################################
###########CALORIES########################
###########################################
$calories= new ChartComponent();
$calories->setCaption ("Calories");
$calories->setDataSource($dataSource);
$calories->setCaption("Calories");
$calories->setWidth(4);
$calories->setDataSource($dataSource);
$calories->setYAxis("Calorie", array(
		'adaptiveYMin' => true
));
$calories->setSecondYAxis("Calorie_H", array(
		'adaptiveYMin' => true
));
$calories->setLabelExpression("Date", "date", array(
		'timestampRange' => true,
		'timeUnit' => 'month',
                'customTimeUnitPath' => array('month', 'day'),
                'autoDrill' => true
));
$calories->addSeries("Calorie", "Calories", array(
		'displayType' => "Line",
		'decimals' => 0 ,
		'aggregateFunction' => "SUM"
));
$calories->addSeries("Calorie_H", "cal_H", array(
		'displayType' => "Line",
                'onSecondYAxis' => true,
		'decimals' => 0 ,
		'aggregateFunction' => "AVG"
));
$calories->setOption('showValues', false);
Dashboard::addComponent($calories);
$filter->addFilterTo($calories);
###########################################
############END CALORIES ##################
###########################################
###########################################
############START SPORT CALORIES ##########
###########################################

$genreChart = new ChartComponent();
$genreChart->setWidth(4);
$genreChart->setCaption("Calories par sport");
$genreChart->setDataSource($dataSource);
$genreChart->setLabelExpression("Date", "date", array(
		'timestampRange' => true,
		'timeUnit' => 'month',
                'customTimeUnitPath' => array('month', 'day'),
                'autoDrill' => true
));
/*
$genreChart->addSeries("escalade", 'Calories', array('condition' => "sport_name = 'escalade'", 'displayType' => 'Line' ,'showValues'=> false));
$genreChart->addSeries("natation", 'Calories', array('condition' => "sport_name = 'natation'", 'displayType' => 'Line','showValues'=> false));
$genreChart->addSeries("Footing", 'Calories', array('condition' => "sport_name = 'Footing'", 'displayType' => 'Line','showValues'=> false));
$genreChart->addSeries("velo", 'Calories', array('condition' => "sport_name = 'velo'", 'displayType' => 'Line','showValues'=> false));
$genreChart->addSeries("Rando", 'Calories', array('condition' => "sport_name = 'Rando'", 'displayType' => 'Line','showValues'=> false));

$genreChart->addSeries("escalade cal_H", 'cal_H', array('condition' => "sport_name = 'escalade'", 'displayType' => 'Line','aggregateFunction' => "AVG",'showValues'=> false));
$genreChart->addSeries("natation cal_H", 'cal_H', array('condition' => "sport_name = 'natation'", 'displayType' => 'Line' ,'aggregateFunction' => "AVG",'showValues'=> false));
$genreChart->addSeries("Footing cal_H", 'cal_H', array('condition' => "sport_name = 'Footing'", 'displayType' => 'Line' ,'aggregateFunction' => "AVG",'showValues'=> false));
$genreChart->addSeries("velo cal_H", 'cal_H', array('condition' => "sport_name = 'velo'", 'displayType' => 'Line' ,'aggregateFunction' => "AVG",'showValues'=> false));
$genreChart->addSeries("Rando cal_H", 'cal_H', array('condition' => "sport_name = 'Rando'", 'displayType' => 'Line' ,'aggregateFunction' => "AVG",'showValues'=> false));
*/
$result_sports = mysql_query($query_sports) or die("La requete $query_sports a echouee");
while ($row_sport=mysql_fetch_array($result_sports, MYSQL_NUM) )
	{
$genreChart->addSeries("$row_sport[1]", 'Calories', array('condition' => "sport_name = '$row_sport[1]'", 'displayType' => 'Line' ,'showValues'=> false));	
$genreChart->addSeries("$row_sport[1] cal_H", 'cal_H', array('condition' => "sport_name = '$row_sport[1]'", 'displayType' => 'Line','aggregateFunction' => "AVG",'showValues'=> false));	
	}


$genreChart->setOption('showValues', false);
Dashboard::addComponent($genreChart);
$filter->addFilterTo($genreChart);
###########################################
############  END SPORT CALORIES ##########
###########################################

###########################################
############START SPORT TIME ##########
###########################################

$genreTimeChart = new ChartComponent();
$genreTimeChart->setWidth(4);
$genreTimeChart->setCaption("Durée par sport");
$genreTimeChart->setDataSource($dataSource);
$genreTimeChart->setLabelExpression("Date", "date", array(
		'timestampRange' => true,
		'timeUnit' => 'month',
                'customTimeUnitPath' => array('month', 'day'),
                'autoDrill' => true
));

$result_sports = mysql_query($query_sports) or die("La requete $query_sports a echouee");
while ($row_sport=mysql_fetch_array($result_sports, MYSQL_NUM) )
	{
$genreTimeChart->addSeries("$row_sport[1]", 'duration', array('condition' => "sport_name = '$row_sport[1]'", 'displayType' => 'Line' ,'showValues'=> false));	
	}


$genreTimeChart->setOption('showValues', false);
Dashboard::addComponent($genreTimeChart);
$filter->addFilterTo($genreTimeChart);
###########################################
############  END SPORT TIME    ##########
###########################################
###########################################
############START SPORT DIST ##########
###########################################

$genredistChart = new ChartComponent();
$genredistChart->setWidth(4);
$genredistChart->setCaption("Distance par sport");
$genredistChart->setDataSource($dataSource);
$genredistChart->setLabelExpression("Date", "date", array(
		'timestampRange' => true,
		'timeUnit' => 'month',
                'customTimeUnitPath' => array('month', 'day'),
                'autoDrill' => true
));

$result_sports = mysql_query($query_sports) or die("La requete $query_sports a echouee");
while ($row_sport=mysql_fetch_array($result_sports, MYSQL_NUM) )
	{
$genredistChart->addSeries("$row_sport[1]", 'DIST_KM', array('condition' => "sport_name = '$row_sport[1]'", 'displayType' => 'Line' ,'showValues'=> false));	
	}


$genredistChart->setOption('showValues', false);
Dashboard::addComponent($genredistChart);
$filter->addFilterTo($genredistChart);
###########################################
############  END SPORT DIST    ##########
###########################################
###########################################
############START SPORT altitude ##########
###########################################

$genrealtChart = new ChartComponent();
$genrealtChart->setWidth(4);
$genrealtChart->setCaption("Dénivelé par sport");
$genrealtChart->setDataSource($dataSource);
$genrealtChart->setLabelExpression("Date", "date", array(
		'timestampRange' => true,
		'timeUnit' => 'month',
                'customTimeUnitPath' => array('month', 'day'),
                'autoDrill' => true
));

$result_sports = mysql_query($query_sports) or die("La requete $query_sports a echouee");
while ($row_sport=mysql_fetch_array($result_sports, MYSQL_NUM) )
	{
$genrealtChart->addSeries("$row_sport[1]", 'altitude', array('condition' => "sport_name = '$row_sport[1]'", 'displayType' => 'Line' ,'showValues'=> false));	
	}


$genrealtChart->setOption('showValues', false);
Dashboard::addComponent($genrealtChart);
$filter->addFilterTo($genrealtChart);
###########################################
############  END SPORT altitude    ##########
###########################################

$sports_dur= new ChartComponent();
$sports_dur->setCaption ("Sports par durée");
$sports_dur->setDataSource($dataSource);

$sports_dur->setLabelExpression("Sports", "sport_name");
$sports_dur->addSeries("Sports", "duration / 3600 ", array(
                'displayType' => "Pie",
		'sort' => "DESC"
));
$sports_dur->setOption('limit', 10);
Dashboard::addComponent($sports_dur);
$filter->addFilterTo($sports_dur);




Dashboard::Render();






?>
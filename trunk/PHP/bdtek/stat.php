<HTML>
   <HEAD>
         <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	
          <link rel="stylesheet" href="style.css" type="text/css">

   </HEAD>
   <body bgcolor='#f4d7b7'>
	   <H1>Statistiques de la BDtek</H1>	
 

<?php

define('PUN_ROOT', './');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
// Width of the chart
$width = 200;


   /* Connecting, selecting database */
 
$link=connect_db($db_host, $db_username, $db_password, $db_name);
$query_scenariste = "SELECT count(distinct(scenariste.scenariste_id)) FROM scenariste";
$result_scenariste = mysql_query($query_scenariste) or die("La requête scenariste a echouée");
$query_dessinateur = "SELECT count(distinct(dessinateur.dessinateur_id)) FROM dessinateur";
$result_dessinateur = mysql_query($query_dessinateur) or die("La requête dessinateur a echouée");$query_serie = "SELECT count(distinct(serie.serie_id)) FROM serie";
$result_serie = mysql_query($query_serie) or die("La requête serie a echouée");	
$query_album = "SELECT count(distinct(album.album_id)) FROM album";
$result_album = mysql_query($query_album) or die("La requête album a echouée");	
$query_maison = "SELECT count(distinct(maison_edition.maison)) FROM maison_edition";
$result_maison = mysql_query($query_maison) or die("La requête maison_edition a echouée");	
$query_genre = "SELECT count(distinct(genre.id)) FROM genre";
$result_genre = mysql_query($query_genre) or die("La requête genre a echouée");

$query_nb_maison = " SELECT maison_edition.maison, sum(A.nb) \"nbtot\" FROM maison_edition LEFT JOIN (SELECT album.maison_id \"idm\", count(album_id) \"nb\"  FROM album  group by  album.maison_id ) A ON A.idm=maison_edition.maison_id group by maison_edition.maison order by nbtot desc";
$result_nb_maison = mysql_query($query_nb_maison) or die("La requête nb maison a echouée"); 
$query_nb_scenariste = " SELECT scenariste.nom, sum(A.nb) \"nbtot\" FROM scenariste LEFT JOIN (SELECT scenariste_id \"idm\", count(album_id) \"nb\"  FROM album_scenariste  group by  scenariste_id  ) A ON A.idm=scenariste_id  group by scenariste.nom order by nbtot desc";
$result_nb_scenariste = mysql_query($query_nb_scenariste) or die("La requête nb scenariste a echouée"); 
$query_nb_dessinateur = " SELECT dessinateur.nom, sum(A.nb) \"nbtot\" FROM dessinateur LEFT JOIN (SELECT dessinateur_id \"idm\", count(album_id) \"nb\"  FROM album_dessinateur  group by  dessinateur_id  ) A ON A.idm=dessinateur_id  group by dessinateur.nom order by nbtot desc";
$result_nb_dessinateur = mysql_query($query_nb_dessinateur) or die("La requête nb dessinateur a echouée"); 
$query_nb_serie = " SELECT serie.nom, sum(A.nb) \"nbtot\" FROM serie LEFT JOIN (SELECT album.serie_id \"idm\", count(album_id) \"nb\"  FROM album  group by  album.serie_id ) A ON A.idm=serie.serie_id group by serie.nom order by nbtot desc ";
$result_nb_serie = mysql_query($query_nb_serie) or die("La requête nb serie a echouée"); 
$query_nb_genre = " SELECT genre.genre, sum(A.nb) \"nbtot\" FROM genre INNER JOIN (SELECT genre_id \"idm\", count(album_id) \"nb\"  FROM album_genre  group by  genre_id  ) A ON A.idm=id  group by genre.genre order by nbtot desc";
$result_nb_genre = mysql_query($query_nb_genre) or die("La requête nb genre a echouée"); 
  /* Printing results in HTML */
print "<table border=2>\n";
printf("<TR><TD>Nb de maisons d'édition</TD><TD>%s</TD></TR>\n", mysql_result($result_maison,0) );
printf("<TR><TD>Nb de dessinateurs</TD><TD>%s</TD></TR>\n", mysql_result($result_dessinateur,0) );
	printf("<TR><TD>Nb de scénaristes</TD><TD>%s</TD></TR>\n", mysql_result($result_scenariste,0) );
	
	printf("<TR><TD>Nb de series</TD><TD>%s</TD></TR>\n", mysql_result($result_serie,0)-1 );
	printf("<TR><TD>Nb d'albums</TD><TD>%s</TD></TR>\n", mysql_result($result_album,0) );
	printf("<TR><TD>Nb de genre</TD><TD>%s</TD></TR>\n", mysql_result($result_genre,0)+1 );
	 print "</table>\n";
	 print "<H2>Nb d'albums par éditeurs</H2>\n";
//	 print "<table border=2>\n";
//print"<TR><TD><FONT COLOR=BLUE>Editeur</FONT></TD><TD><FONT COLOR=BLUE>NB albums</FONT></TD>";
$i=0;
 while ($row = mysql_fetch_array($result_nb_maison, MYSQL_NUM))
         {
         	if ($i>0)
         	{
         	$labelm=$labelm."*";
         	$datam=$datam."*";
         	}
         //	printf("<TR><TD>%s</TD><TD>%s</TD></TR>", $row[0],$row[1] );
         	$labelm=$labelm.$row[0];
         	$datam=$datam.$row[1];
         	$i++;
         }
     //  print "</table>\n"; 
   printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labelm, $datam);
print "<H2>Nb d'albums par genre</h2>\n";
//print "<table border=2>\n";
//print"<TR><TD><FONT COLOR=BLUE>dessinateur</FONT></TD><TD><FONT COLOR=BLUE>NB albums</FONT></TD>";
$i=0;
$classe=0;
 while ($row = mysql_fetch_array($result_nb_genre, MYSQL_NUM))
         {
         $classe=$classe+$row[1];	
         	//printf("<TR><TD>%s</TD><TD>%s</TD></TR>", $row[0],$row[1] );
            if ($i>0)
         	{
         	$labelg=$labelg."*";
         	$datag=$datag."*";
         	}
         	$labelg=$labelg.$row[0];
         	$datag=$datag.$row[1];
         	$i++;
         }
         $nonclasse=mysql_result($result_album,0)-$classe;
         $labelg=$labelg."*Inclassable";
         	$datag=$datag."*".$nonclasse;
        printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labelg, $datag);
         
       //print "</table>\n";   
  
print "<H2>Nb d'albums par dessinateurs</h2>\n";
//print "<table border=2>\n";
//print"<TR><TD><FONT COLOR=BLUE>dessinateur</FONT></TD><TD><FONT COLOR=BLUE>NB albums</FONT></TD>";
$i=0;
 while ($row = mysql_fetch_array($result_nb_dessinateur, MYSQL_NUM))
         {
         	//printf("<TR><TD>%s</TD><TD>%s</TD></TR>", $row[0],$row[1] );
            if ($i>0)
         	{
         	$labeld=$labeld."*";
         	$datad=$datad."*";
         	}
         	$labeld=$labeld.$row[0];
         	$datad=$datad.$row[1];
         	$i++;
         }
        printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labeld, $datad);
         
       //print "</table>\n";   
       print "<H2>Nb d'albums par scenariste</H2>\n";
//print "<table border=2>\n";
//print"<TR><TD><FONT COLOR=BLUE>scenariste</FONT></TD><TD><FONT COLOR=BLUE>NB albums</FONT></TD>";
 
 $i=0;
 while ($row = mysql_fetch_array($result_nb_scenariste, MYSQL_NUM))
         {
  //       	printf("<TR><TD>%s</TD><TD>%s</TD></TR>", $row[0],$row[1] );
    if ($i>0)
         	{
         	$labels=$labels."*";
         	$datas=$datas."*";
         	}
         	$labels=$labels.$row[0];
         	$datas=$datas.$row[1];
         	$i++;
         }
        printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labels, $datas);
  
//print "</table>\n";  


    
print "<H2>Nb d'albums par serie</H2>\n";
//	 print "<table border=2>\n";
//print"<TR><TD><FONT COLOR=BLUE>Serie</FONT></TD><TD><FONT COLOR=BLUE>NB albums</FONT></TD>";
 $i=0;
 $data_autres=0;
 while ($row = mysql_fetch_array($result_nb_serie, MYSQL_NUM))
         {
  //       	printf("<TR><TD>%s</TD><TD>%s</TD></TR>", $row[0],$row[1] );
            // if (($i>0)AND ($row[1]/(mysql_result($result_album,0)) > 0.01))
         	if ($i>0)
         	{
         	$labelse=$labelse."*";
         	$datase=$datase."*";
         	}
         	//if ($row[1]/(mysql_result($result_album,0)) > 0.01)
         	//{
         	$labelse=$labelse.$row[0];
         	$datase=$datase.$row[1];
         //	}
         	//else
         	//{
         //	$data_autres=$data_autres+$row[1];	
         	//}
         	$i++;
         }
		//	$labelse=$labelse."*AUTRES";
         //	$datase=$datase."*".$data_autres;
    //     print "</table>\n";
       printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labelse, $datase);
       ;             
 /* Free resultset */
 mysql_free_result($result_nb_maison);
  mysql_free_result($result_nb_scenariste);
   mysql_free_result($result_nb_dessinateur);
   mysql_free_result($result_nb_serie);
 /*	
 SELECT scenariste.nom FROM scenariste order by scenariste.nom 
 
 SELECT maison_edition.maison, A.nb FROM maison_edition,(SELECT album.maison_id "idm", count(album_id) "nb"  FROM album  group by  album.maison_id ) A WHERE A.idm=maison_edition.maison_id and maison_edition.maison_id=A.idm
  $query = "SELECT album.album_id, 
			serie.nom,
			album.place,
			album.url,
			album.nom,
			maison_edition.maison,
			maison_edition.collection,
			dessinateur.nom,
			scenariste.nom 
			FROM serie,album, maison_edition, dessinateur,album_dessinateur,scenariste,album_scenariste 
			WHERE   album.serie_id=serie.serie_id 
			AND album.maison_id=maison_edition.maison_id  
			AND maison_edition.maison_id=album.maison_id 
			AND album.album_id=album_dessinateur.album_id  
			AND album_dessinateur.album_id=album.album_id 
			AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
			AND  dessinateur.dessinateur_id=album_dessinateur.dessinateur_id 
			AND album.album_id=album_scenariste.album_id  
			AND album_scenariste.album_id=album.album_id 
			AND album_scenariste.scenariste_id=scenariste.scenariste_id 
			AND scenariste.scenariste_id=album_scenariste.scenariste_id 
			ORDER BY $ordre";
     $result = mysql_query($query) or die("La requête a echouée");
 /* Closing connection */
  mysql_close($link);

?>

	   </BODY>
 </HTML>

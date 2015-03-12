<HTML>
   <HEAD>
         <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	
          <link rel="stylesheet" href="style.css" type="text/css">

   </HEAD>
   <body bgcolor='#f4d7b7'>
	   <H1>Affichage de tous les albums</H1>	
 

<?php
define('PUN_ROOT', './');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);
	
	/*$query = "CREATE TABLE genre (
     id int(5) NOT NULL AUTO_INCREMENT,
     genre text NOT NULL,
     PRIMARY KEY (id) )";*/
	//$query = "describe genre";
	$query ="SELECT album.album_id, album.nom FROM album, serie WHERE serie.nom LIKE '%Skarbek%' AND  album.serie_id=serie.serie_id"; 
	//$query = "SELECT * FROM genre";// LEFT OUTER JOIN genre ON album_genre.genre_id = genre.id  ";
//$query = "SELECT * FROM album_genre, genre WHERE album_genre.genre_id=genre.id";
/*	$query = "SELECT * FROM  (SELECT album.album_id , 
serie.nom \"serie\",
album.place,
album.url,
album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,
scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE album.album_id LIKE '28' 
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN album_genre  ON  A.album_id = album_genre.album_id";*/
$genre_id=8;
	 print "<table border=2>\n";
	 $result = mysql_query($query);
	 while ($row = mysql_fetch_array($result, MYSQL_NUM))
         {
         	mysql_query("INSERT INTO album_genre VALUES ('$row[0]', '$genre_id')")or die("\n Echec INSERT genre");
         	printf("<TR> ");
         	for ($i=0 ; $i < 2 ; $i++)
         	{
         printf("<td> %s </td><td> %s </td>",$genre_id, $row[$i]);
         }
         printf("</TR>");
         }
         print "</table></body></html>";
         
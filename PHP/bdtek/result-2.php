<html>
  <head>
   <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
       <title></title>
  </head>

  <body bgcolor='#f4d7b7'>

<?php
$critere='';
$type='';
$ordre='serie, place';
define('PUN_ROOT', './');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';

if (isset($_GET['order']))
{
	if ($_GET['order'] != '')
	{
		$ordre=$_GET['order'];
	}
	else{
		$ordre='serie, place';
		}
}
echo "Tri par $ordre\n";


if (isset($_POST['critere']))
{
$critere=$_POST['critere'];
}
if (! $critere)
{
	if (isset($_GET['critere']))
{
$critere=$_GET['critere'];
}
}

if (isset($_POST['type']))
{
$type=$_POST['type'];
}
if (! $type)
{
	if (isset($_GET['type']))
{
$type=$_GET['type'];
}
}


	
echo"<H1>Albums dont $type contient $critere: </h1>";

 /* Connecting, selecting database */
 $link=connect_db($db_host, $db_username, $db_password, $db_name);
if ($type === "l album")
{
		$query = "SELECT * FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE album.nom LIKE '%$critere%' 
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id
ORDER BY $ordre";
$query_album = "SELECT count(distinct(album.album_id))FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE album.nom LIKE '%$critere%' 
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id
ORDER BY $ordre";
}
if ($type === "la série")
{
	$query = "SELECT * FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE serie.nom LIKE '%$critere%' 
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id
ORDER BY $ordre";
$query_album = "SELECT count(distinct(album.album_id))FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE serie.nom LIKE '%$critere%' 
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id
ORDER BY $ordre";

}
if ($type === "le dessinateur")
{
	$query = "SELECT * FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE dessinateur.nom LIKE '%$critere%' 
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id
ORDER BY $ordre";
$query_album = "SELECT count(distinct(album.album_id)) FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE dessinateur.nom LIKE '%$critere%' 
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id
ORDER BY $ordre";
}
if ($type === "le scenariste")
{
	$query = "SELECT * FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE scenariste.nom LIKE '%$critere%' 
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id	
ORDER BY $ordre";
$query_album = "SELECT count(distinct(album.album_id)) FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE scenariste.nom LIKE '%$critere%' 
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id	
ORDER BY $ordre";

}
if ($type === "l éditeur")
{
	
	$query = "SELECT * FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE maison_edition.maison LIKE '%$critere%'
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id
ORDER BY $ordre";
$query_album = "SELECT count(distinct(album.album_id)) FROM (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE maison_edition.maison LIKE '%$critere%'
AND  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A LEFT OUTER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id) B  ON B.album_id = A.album_id
ORDER BY $ordre";


}
if ($type === "le genre")
{
	$query = "SELECT * FROM  (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A INNER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id
AND genre.genre LIKE '%$critere%') B  ON B.album_id = A.album_id
ORDER BY $ordre";
$query_album = "SELECT count(distinct(album.album_id)) FROM (SELECT album.album_id , 
serie.nom \"serie\",album.place,album.url,album.nom \"albumnom\",
maison_edition.maison,
maison_edition.collection,
dessinateur.nom \"dessinateur\" ,scenariste.nom \"scenariste\"
FROM serie, album, maison_edition, dessinateur, album_dessinateur,scenariste,album_scenariste
WHERE  album.serie_id=serie.serie_id  
AND album.maison_id=maison_edition.maison_id  
AND album.album_id=album_dessinateur.album_id  
AND album_dessinateur.dessinateur_id=dessinateur.dessinateur_id 
AND album.album_id=album_scenariste.album_id  
AND album_scenariste.scenariste_id=scenariste.scenariste_id) A INNER JOIN (SELECT album_genre.album_id, genre FROM album_genre, genre WHERE album_genre.genre_id= genre.id
AND genre.genre LIKE '%$critere%') B  ON B.album_id = A.album_id
ORDER BY $ordre";

}

$nb = mysql_query($query_album) or die("La requête album a echouée");	
   $result = mysql_query($query) or die("La requ?te a echouée");
	print"Il y a $nb albums qui correspondent a la recherche";
	
  /* Printing results in HTML */
      print "<table border=2>\n";
print"<TR>
<TD><FONT COLOR=BLUE><a href=\"result.php?type=$type&critere=$critere\">série</a></FONT></TD>
<TD><FONT COLOR=BLUE>place</FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result.php?type=$type&critere=$critere&order=albumnom\">album</a></FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result.php?type=$type&critere=$critere&order=maison\">Edition</a></FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result.php?type=$type&critere=$critere&order=collection\">collection</a></FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result.php?type=$type&critere=$critere&order=dessinateur\">dessinateur</a></FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result.php?type=$type&critere=$critere&order=scenariste\">scénariste</a></FONT> </TD>
<TD><FONT COLOR=BLUE><a href=\"result.php?type=$type&critere=$critere&order=genre\">genre</a></FONT> </TD>
<TD><FONT COLOR=BLUE>Action </FONT></TD>
</TR>";
      while ($row = mysql_fetch_array($result, MYSQL_NUM))
         {
		 if($row[3]){
	  printf("<TR> <FORM ACTION=\"admin/update_album.php\" METHOD=\"POST\">
<INPUT TYPE=\"hidden\" NAME=\"album\" value= %s > </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"serie\" value='%s' >%s </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"place\" value='%s' >%s </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"nom\" value='%s' >
	<INPUT TYPE=\"hidden\" NAME=\"url\" value='%s' ><a href='%s'>%s</a> </TD> 

<TD> <INPUT TYPE=\"hidden\" NAME=\"maison\" value='%s' >%s </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"collection\" value='%s' >%s </TD>
<TD> <INPUT TYPE=\"hidden\" NAME=\"dessinateur\" value='%s' >%s </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"scenariste\" value='%s' >%s </TD>
<TD> <INPUT TYPE=\"hidden\" NAME=\"genre\" value='%s' >%s </TD>
<TD><INPUT TYPE=\"SUBMIT\" NAME=\"ACTION\" VALUE=\"Modifier\"></TD>
</form>
</TR>",  $row[0] , $row[1], $row[1] ,$row[2], $row[2] , $row[4],$row[3], $row[3], $row[4] , $row[5], $row[5] ,  $row[6], $row[6] ,  $row[7], $row[7] , $row[8], $row[8], $row[10],$row[10]);
      
	  }else{
	  printf("<TR> <FORM ACTION=\"admin/update_album.php\" METHOD=\"POST\">
<INPUT TYPE=\"hidden\" NAME=\"album\" value= %s > </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"serie\" value='%s' >%s </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"place\" value='%s' >%s </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"nom\" value='%s' >%s </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"maison\" value='%s' >%s </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"collection\" value='%s' >%s </TD>
<TD> <INPUT TYPE=\"hidden\" NAME=\"dessinateur\" value='%s' >%s </TD> 
<TD> <INPUT TYPE=\"hidden\" NAME=\"scenariste\" value='%s' >%s </TD>
<TD> <INPUT TYPE=\"hidden\" NAME=\"genre\" value='%s' >%s </TD>
<TD><INPUT TYPE=\"SUBMIT\" NAME=\"ACTION\" VALUE=\"Modifier\"></TD>
</form>
</TR>",  $row[0] , $row[1], $row[1] , $row[2] , $row[2], $row[4], $row[4] , $row[5], $row[5] ,  $row[6], $row[6] ,  $row[7], $row[7] , $row[8], $row[8], $row[10],$row[10]);
 
	  }
	 }
	 print "</table>\n";
/* Free resultset */
 mysql_free_result($result);
 /* Closing connection */
  mysql_close($link);

?>
</body>
</html>
<html>
  <head>
       <title></title>
  </head>

  <body bgcolor='#f4d7b7'>

<?php
$serie='';
$ordre='serie.nom, album.place';
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
		$ordre='serie.nom, album.place';
		}
}

echo "Tri par $ordre\n";
if (isset($_POST['serie']))
{
$serie=$_POST['serie'];
}
if (! $serie)
{
	if (isset($_GET['serie']))
{
$serie=$_GET['serie'];
}

}
if($serie)
{
echo"<H1>Albums dont la série est $serie: </h1>";

 /* Connecting, selecting database */
 $link=connect_db($db_host, $db_username, $db_password, $db_name);

$query = "SELECT album.album_id, 
serie.nom,album.place,album.url,album.nom,
maison_edition.maison,
maison_edition.collection,
dessinateur.nom,scenariste.nom 
FROM serie,album, maison_edition, dessinateur,album_dessinateur,scenariste,album_scenariste 
WHERE serie.nom LIKE '%$serie%' 
AND  album.serie_id=serie.serie_id  
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
	
  /* Printing results in HTML */
      print "<table border=2>\n";
print"<TR>
<TD><FONT COLOR=BLUE><a href=\"result_serie.php?serie=$serie&order='serie.nom, album.place'\">série</a></FONT></TD>
<TD><FONT COLOR=BLUE>place</FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result_serie.php?serie=$serie&order=album.nom\">album</a></FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result_serie.php?serie=$serie&order=maison_edition.maison\">Edition</a></FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result_serie.php?serie=$serie&order=maison_edition.collection\">collection</a></FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result_serie.php?serie=$serie&order=dessinateur.nom\">dessinateur</a></FONT></TD>
<TD><FONT COLOR=BLUE><a href=\"result_serie.php?serie=$serie&order=serie.nom\">scénariste</a></FONT> </TD>
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

<TD><INPUT TYPE=\"SUBMIT\" NAME=\"ACTION\" VALUE=\"Modifier\"></TD>
</form>
</TR>",  $row[0] , $row[1], $row[1] ,$row[2], $row[2] , $row[4],$row[3], $row[3], $row[4] , $row[5], $row[5] ,  $row[6], $row[6] ,  $row[7], $row[7] , $row[8], $row[8]);
      
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

<TD><INPUT TYPE=\"SUBMIT\" NAME=\"ACTION\" VALUE=\"Modifier\"></TD>
</form>
</TR>",  $row[0] , $row[1], $row[1] , $row[2] , $row[2], $row[4], $row[4] , $row[5], $row[5] ,  $row[6], $row[6] ,  $row[7], $row[7] , $row[8], $row[8]);
 
	  }
	 }
	 print "</table>\n";
/* Free resultset */
 mysql_free_result($result);
 /* Closing connection */
  mysql_close($link);
}
else
{
print("<H2>Le champ série doit être non vide!!!</H2>");
}
?>
</body>
</html>
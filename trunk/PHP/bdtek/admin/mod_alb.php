
<?php

/* Connecting, selecting database */
define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);

$faire=1;
if (isset($_POST['album_id']))
{
$album_id=$_POST['album_id'];
}
if (isset($_POST['nom_album']))
{
$nom_album=$_POST['nom_album'];
}
if (isset($_POST['serie']))
{
$serie=$_POST['serie'];
}
if (isset($_POST['place']))
{
$place=$_POST['place'];
}
if (isset($_POST['dessinateur']))
{
$dessinateur=$_POST['dessinateur'];
}
if (isset($_POST['scenariste']))
{
$scenariste=$_POST['scenariste'];
}
if (isset($_POST['maison_ed']))
{
$maison_ed=$_POST['maison_ed'];
}
if (isset($_POST['genre']))
{
$genre=$_POST['genre'];
}
if (isset($_POST['DEL']))
{
$action=$_POST['DEL'];
}
if (isset($_POST['url']))
{
$url=$_POST['url'];
}
//echo "$action x\n $album_id x\n $serie x\n $nom_album\n x $maison_ed x\n $scenariste x\n $dessinateur" ;
if ($action == "Supprimer" )
{
 mysql_query("DELETE FROM album WHERE album_id = '$album_id'")or die("\n Echec DEL album");
 mysql_query("DELETE FROM album_dessinateur WHERE album_id = '$album_id'")or die("\n Echec DEL Dessinateur");
 mysql_query("DELETE FROM album_scenariste WHERE album_id = '$album_id'")or die("\n Echec DEL Scenariste");
  mysql_query("DELETE FROM album_genre WHERE album_id = '$album_id'")or die("\n Echec DEL Genre");
  print("<H2>L'album $nom_album est  supprimé</H2>");
}
else{
	 
if(strcmp("$serie","ONE SHOT")==0){ $place=0;}
if ($serie && $scenariste && $dessinateur && $maison_ed && $nom_album ){
   if (!$place && $place!=0){
      if(strcmp("$serie","aucune serie")!=0){
           print("<H2>Il manque la place de l'album dans la série!!</H2>");}}
    $maison = strtok($maison_ed,"#");
    $collection = strtok("#");
    $query_serie = "SELECT serie_id FROM serie WHERE nom LIKE '$serie'";
    $result_serie = mysql_query($query_serie) or die("La requête serie_id a echouée");
    $temp = mysql_fetch_array($result_serie, MYSQL_NUM);
    $serie_id = $temp[0];
 
    $query_maison_id = "SELECT maison_id FROM maison_edition WHERE
    maison LIKE '$maison' AND collection LIKE '$collection'";
    $result_maison = mysql_query($query_maison_id) or die("La requête maison_id a echouée");
    $temp1 = mysql_fetch_array($result_maison, MYSQL_NUM);
    $maison_id = $temp1[0];

    $query_des = "SELECT dessinateur_id FROM dessinateur WHERE nom LIKE '$dessinateur'";
    $result_des = mysql_query($query_des) or die("La requête dessinateur_id a echouée");
    $temp1 = mysql_fetch_array($result_des, MYSQL_NUM);
    $dessinateur_id = $temp1[0];

    $query_sce = "SELECT scenariste_id FROM scenariste WHERE nom LIKE '$scenariste'";
    $result_sce = mysql_query($query_sce) or die("La requête scenariste_id a echouée");
    $temp1 = mysql_fetch_array($result_sce, MYSQL_NUM);
    $scenariste_id = $temp1[0];

if ($genre)
{
$query_genre = "SELECT id FROM genre WHERE genre LIKE '$genre'";
$result_genre = mysql_query($query_genre) or die("La requête recherch genre a echouée");
$genre_id = mysql_result($result_genre,0);
}
    mysql_query("UPDATE album SET serie_id = '$serie_id', place='$place', nom='$nom_album', 		maison_id='$maison_id',	url='$url' WHERE album_id='$album_id'")	or die("\n Echec MAJ album");
    mysql_query("UPDATE album_dessinateur SET dessinateur_id='$dessinateur_id' WHERE album_id = '$album_id'")or die("\n Echec MAJ dessinateur");
	mysql_query("UPDATE album_scenariste SET scenariste_id='$scenariste_id' WHERE album_id = '$album_id'")or die("\n Echec MAJ scenariste");
	$query_test2="SELECT * FROM album_genre WHERE album_id ='$album_id'";
    $result_test2 = mysql_query($query_test2) or die("La requête test2 a échouée");
    $MAJ=0;
    while ($row = mysql_fetch_array($result_test2, MYSQL_NUM))
    {	
    	$MAJ=1;
    }
    if($MAJ == 1)
    	{
    		//echo "$MAJ\n$genre_id\n$album_id";
    		if($genre_id)
    		{
				mysql_query("UPDATE album_genre SET genre_id='$genre_id' WHERE album_id = '$album_id'")or die("\n Echec MAJ genre");
    		}
    		else
    		{
    			mysql_query("DELETE FROM album_genre WHERE album_id = '$album_id'")or die("\n Echec DEL genre");
    		}
    	}
    else
    	{
    		mysql_query("INSERT INTO album_genre VALUES ('$album_id', '$genre_id')")or die("\n Echec INSERT genre");
    	}
   print("<H2>L'album $nom_album est  mis a jour</H2>");
   }
else
{
print("<H2>Tous les champs (sauf peut être Place) doivent être non vide!!!</H2>");
}
}
?>
<html>
<head>
         <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	

<title></title>
</head>

<body bgcolor='#f4d7b7'>
</body>
</html>
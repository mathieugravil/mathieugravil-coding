<html>
  <head>
   <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
       <title></title>
  </head>

  <body bgcolor='#f4d7b7'>
</body>
<?php
$faire=1;

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
if (isset($_POST['maison']))
{
$maison_ed=$_POST['maison'];
}
if (isset($_POST['url']))
{
$url=$_POST['url'];
}

define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';

if(strcmp("$serie","aucune serie")==0)
  {
  $place=0;
  }
if ($serie && $scenariste && $dessinateur && $maison_ed && $nom_album )
{
   if (!$place && $place!=0)
    {
      if(strcmp("$serie","aucune serie")!=0)
         {
           print("<H2>Il manque la place de l'album dans la série!!</H2>");
         }
    }
   else
   {
    $maison = strtok($maison_ed,"#");
    $collection = strtok("#");
/* Connecting, selecting database */
    $link=connect_db($db_host, $db_username, $db_password, $db_name);
    print 'Accès à la base [<FONT COLOR=GREEN>OK</FONT>]<BR>';


    $query_serie = "SELECT serie_id FROM serie WHERE nom LIKE '$serie'";
    $result_serie = mysql_query($query_serie) or die("La requête serie_id a echouée");
    $temp = mysql_fetch_array($result_serie, MYSQL_NUM);
    $serie_id = $temp[0];
    echo"$serie_id <BR>";  


    $query_maison_id = "SELECT maison_id FROM maison_edition WHERE
    maison LIKE '$maison' AND collection LIKE '$collection'";
    $result_maison = mysql_query($query_maison_id) or die("La requête maison_id a echouée");
    $temp1 = mysql_fetch_array($result_maison, MYSQL_NUM);
    $maison_id = $temp1[0];
    echo"$maison_id <BR>";  
 
    $query_des = "SELECT dessinateur_id FROM dessinateur WHERE nom LIKE '$dessinateur'";
    $result_des = mysql_query($query_des) or die("La requête dessinateur_id a echouée");
    $temp1 = mysql_fetch_array($result_des, MYSQL_NUM);
    $dessinateur_id = $temp1[0];
    echo"$dessinateur_id <BR>";  

    $query_sce = "SELECT scenariste_id FROM scenariste WHERE nom LIKE '$scenariste'";
    $result_sce = mysql_query($query_sce) or die("La requête scenariste_id a echouée");
    $temp1 = mysql_fetch_array($result_sce, MYSQL_NUM);
    $scenariste_id = $temp1[0];
    echo"$scenariste_id <BR>"; 

    $query_test = "SELECT nom  FROM album WHERE
    maison_id = '$maison_id' AND serie_id = '$serie_id' AND nom LIKE '$nom_album'";
    $result_test = mysql_query($query_test) or die("La requête test a échouée");
    while ($row = mysql_fetch_array($result_test, MYSQL_NUM))
    {
    print("<H2> L'album est déja dans la base de donnée </H2>");
    $faire=0;
    }
    if($faire == 1)
    {
    mysql_query("INSERT INTO album values('','$serie_id','$place','$nom_album','$maison_id','$url')");
    $query_aid = "SELECT album_id  FROM album WHERE maison_id = '$maison_id' AND serie_id = '$serie_id' AND nom='$nom_album' AND place='$place'";
    $result_aid = mysql_query($query_aid) or die("La requête aid a échouée");
    $temp1 = mysql_fetch_array($result_aid, MYSQL_NUM);
    $album_id = $temp1[0];
    echo"$album_id <BR>";
    mysql_query("INSERT INTO album_dessinateur values('$album_id','$dessinateur_id')");
    mysql_query("INSERT INTO album_scenariste values('$album_id','$scenariste_id')");
    print("<H2>L'album  est  rentrée dans la base</H2>");
    }
   }
}
else
{
print("<H2>Tous les champs (sauf peut être Place) doivent être non vide!!!</H2>");
}
?>

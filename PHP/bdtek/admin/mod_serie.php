
<?php

/* Connecting, selecting database */
   define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);
   
   $faire=1;
	
if (isset($_POST['serie_id']))
{
$serie_id=$_POST['serie_id'];
}
if (isset($_POST['nom_serie']))
{
$nom_serie=$_POST['nom_serie'];
}
if ($nom_serie){
mysql_query("UPDATE serie SET nom = '$nom_serie' WHERE serie_id='$serie_id'") or die("\n Echec MAJ série");
printf("<H2>Le nouveau nom de la série est $nom_serie </H2>");

}else{
print("<H2>Le champ doit être non vide!!!</H2>");
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
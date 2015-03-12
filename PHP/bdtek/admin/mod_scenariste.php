
<?php

/* Connecting, selecting database */
    define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);
   
$faire=1;
	
if (isset($_POST['scenariste_id']))
{
$scenariste_id=$_POST['scenariste_id'];
}
if (isset($_POST['nom_scenariste']))
{
$nom_scenariste=$_POST['nom_scenariste'];
}
if ($nom_scenariste){
mysql_query("UPDATE scenariste SET nom = '$nom_scenariste' WHERE scenariste_id='$scenariste_id'") or die("\n Echec MAJ");
printf("<H2>Le nouveau nom du scénariste est $nom_scenariste </H2>");

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
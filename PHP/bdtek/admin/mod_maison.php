<?php

/* Connecting, selecting database */
    
     define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);

    $faire=1;
	
if (isset($_POST['maison_id']))
{
$maison_id=$_POST['maison_id'];
}
if (isset($_POST['nom_maison']))
{
$nom_maison=$_POST['nom_maison'];
}
if (isset($_POST['nom_collection']))
{
$nom_collection=$_POST['nom_collection'];
}
if ($nom_maison){

mysql_query("UPDATE maison_edition 
			SET maison = '$nom_maison', 
			collection='$nom_collection'  
			WHERE maison_id='$maison_id'") 
			or die("\n Echec MAJ");
printf("<H2>Le nouveau nom de l'éditeur est $nom_maison $nom_collection </H2>");

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

<?php

/* Connecting, selecting database */
   define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);
   
   $faire=1;
	
if (isset($_POST['dessinateur_id']))
{
$dessinateur_id=$_POST['dessinateur_id'];
}
if (isset($_POST['nom_dessinateur']))
{
$nom_dessinateur=$_POST['nom_dessinateur'];
}
if ($nom_dessinateur){
mysql_query("UPDATE dessinateur SET nom = '$nom_dessinateur' WHERE dessinateur_id='$dessinateur_id'") or die("\n Echec MAJ");
printf("<H2>Le nouveau nom du dessinateur est $nom_dessinateur </H2>");

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
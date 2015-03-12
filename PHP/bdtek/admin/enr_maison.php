<html>
  <head>
       <title></title>
  </head>

  <body bgcolor='#f4d7b7'>
</body>
<?php
define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$faire=1;
if (isset($_POST['maison']))
{
$maison=$_POST['maison'];
}
if (isset($_POST['collection']))
{
$collection=$_POST['collection'];
}
if ($maison)
{
/* Connecting, selecting database */
 /* Connecting, selecting database */
    $link=connect_db($db_host, $db_username, $db_password, $db_name);
    print 'Accès à la base [<FONT COLOR=GREEN>OK</FONT>]<BR>';
 $query = "SELECT * FROM maison_edition WHERE maison LIKE '$maison' AND collection LIKE '$collection'";
 $result = mysql_query($query) or die("La requête a echouée");
 while ($row = mysql_fetch_array($result, MYSQL_NUM))
{
print("<H2>La maison et la collection sont déja rentrés dans la
base</H2>");
$faire=0;
}
if($faire == 1)
{
mysql_query("INSERT INTO maison_edition values('','$maison','$collection')");
print("<H2>La maison et la collection sont  rentrés dans la
base</H2>");
}
 mysql_free_result($result);
 /* Closing connection */
  mysql_close($link);
}
else
{
print("<H2>Le champ maison d'édition doit être non vide!!!</H2>");
}
?>

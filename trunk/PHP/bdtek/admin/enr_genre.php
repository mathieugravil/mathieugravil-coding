<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
       <title></title>
  </head>

  <body bgcolor='#f4d7b7'>
</body>
<?php
define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$genre=''; 
$faire=1;
if (isset($_POST['genre']))
{
$genre=$_POST['genre'];
}
if ($genre)
{
/* Connecting, selecting database */
    $link=connect_db($db_host, $db_username, $db_password, $db_name);
    print 'Accés à  la base [<FONT COLOR=GREEN>OK</FONT>]<BR>';
 $query = "SELECT * FROM genre WHERE genre LIKE '$genre'";
 $result = mysql_query($query) or die("La requête a echouée");
 while ($row = mysql_fetch_array($result, MYSQL_NUM))
{
print("<H2>Le genre est dÃ©ja rentrÃ© dans la
base</H2>");
$faire=0;
}
if($faire == 1)
{
mysql_query("INSERT INTO genre values('','$genre')");
print("<H2>Le genre est  rentré dans la
base</H2>");
}
 mysql_free_result($result);
 /* Closing connection */
  mysql_close($link);
}
else
{
print("<H2>Le champ genre doit être non vide!!!</H2>");
}
?>

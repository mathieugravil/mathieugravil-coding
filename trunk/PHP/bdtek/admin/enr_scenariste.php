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

$faire=1;
if (isset($_POST['scenariste']))
{
$scenariste=$_POST['scenariste'];
}
if ($scenariste)
{
/* Connecting, selecting database */
   $link=connect_db($db_host, $db_username, $db_password, $db_name);
	

    print 'Accès à  la base [<FONT COLOR=GREEN>OK</FONT>]<BR>';
 $query = "SELECT * FROM scenariste WHERE nom LIKE '$scenariste'";
 $result = mysql_query($query) or die("La requête a echouée");
while ($row = mysql_fetch_array($result, MYSQL_NUM))
{
print("<H2>Le scénariste est déja rentré dans la
base</H2>");
$faire=0;
}
if($faire == 1)
{
mysql_query("INSERT INTO scenariste values('','$scenariste')");
print("<H2>Le scénariste est  rentré dans la
base</H2>");
}
/* Free resultset */
 mysql_free_result($result);
  /* Closing connection */
  mysql_close($link);
}
else
{
print("<H2>Le champ scénariste doit être non vide!!!</H2>");
}

?>

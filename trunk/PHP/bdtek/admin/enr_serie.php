<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
       <title></title>
  </head>

  <body bgcolor='#f4d7b7'>
</body>
<?php
$faire=1;
define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';

if (isset($_POST['serie']))
{
$serie=$_POST['serie'];
}
if ($serie)
{
/* Connecting, selecting database */
    $link=connect_db($db_host, $db_username, $db_password, $db_name);
	
    print 'Acc�s �� la base [<FONT COLOR=GREEN>OK</FONT>]<BR>';
 $query = "SELECT * FROM serie WHERE nom LIKE '$serie'";
 $result = mysql_query($query) or die("La requ�te a echou�e");
 while ($row = mysql_fetch_array($result, MYSQL_NUM))
{
print("<H2>La s�rie est d�ja rentr�e dans la
base</H2>");
$faire=0;
}
if($faire == 1)
{
mysql_query("INSERT INTO serie values('','$serie')");
print("<H2>La s�rie est  rentr�e dans la
base</H2>");
}
/* Free resultset */
 mysql_free_result($result);
 /* Closing connection */
  mysql_close($link);
}
else
{
print("<H2>Le champ s�rie doit �tre non vide!!!</H2>");
}
?>
</html>
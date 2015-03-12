


<HTML>
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<TITLE></TITLE>
</HEAD>
<BODY bgcolor='#f4d7b7'>
<H1>Modification d'un éditeur</H1>
<TABLE align="center" width="95%" bgcolor="#fcfcfc">
<TR>
	<form action="mod_maison.php" method="post">
<TABLE>
<TR><TD> Nom actuelle de l'éditeur et de la collection:</TD><TD><SELECT NAME="maison_id">

<option>
<?php 
/* Connecting, selecting database */
     define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);

$query_maison = "SELECT * FROM maison_edition ORDER BY maison, collection";
$result_maison = mysql_query($query_maison) or die("La
requête maison a échouée");
while ($row_maison = mysql_fetch_array($result_maison, MYSQL_NUM))
{
	if ($row_maison[1] == $_POST[maison] and $row_maison[2] == $_POST[collection] )
	{
	printf("<OPTION VALUE= %s>",$row_maison[0]);
	}
	else{
	printf("<OPTION VALUE= %s>",$row_maison[0]);
	}
	printf("%s#%s# ",  $row_maison[1],$row_maison[2] );
	print"</OPTION>";
} ?></SELECT> </TD>
</TR>
<TR><TD> Nouveau nom de l'éditeur:</TD><TD> <Input name="nom_maison" type="TEXT" size="30"></TD>
</TR>
</TR>
<TR><TD> Nouveau nom de la collection:</TD><TD> <Input name="nom_collection" type="TEXT" size="30"></TD>
</TR>
<TR>
<TD>  </TD><TD><INPUT TYPE="SUBMIT" NAME="DEL" VALUE="Modifier"></TD>
</TR>
</TABLE>
<?php
/* Free resultset */
 mysql_free_result($result_maison);
 /* Closing connection */
  mysql_close($link);
?>
</BODY>
</HTML>
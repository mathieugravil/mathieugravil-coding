<?php
/* Connecting, selecting database */
   define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);
   

 ?>

<HTML>
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<TITLE></TITLE>
</HEAD>
<BODY bgcolor='#f4d7b7'>
<H1>Modification d'un nom de scenariste</H1>
<TABLE align="center" width="95%" bgcolor="#fcfcfc">
<TR>
	<form action="mod_scenariste.php" method="post">
<TABLE>
<TR><TD> Nom actuelle du scenariste:</TD><TD><SELECT NAME="scenariste_id">

<option>
<?php 
 $query_serie = "SELECT * FROM scenariste ORDER BY nom";
 $result_serie = mysql_query($query_serie) or die("La requ�te scenariste a
 echou�e");
while ($row = mysql_fetch_array($result_serie, MYSQL_NUM))
         {

printf("<OPTION VALUE=%s>",$row[0]);
printf("%s",  $row[1] );
print"</OPTION>";
	 } ?></SELECT> </TD>
</TR>
<TR><TD> Nouveau nom du scenariste:</TD><TD> <Input name="nom_scenariste" type="TEXT" size="30"></TD>
</TR>
<TR>
<TD>   </TD><TD><INPUT TYPE="SUBMIT" NAME="DEL" VALUE="Modifier"></TD>
</TR>
</TABLE>
<?php
/* Free resultset */
 mysql_free_result($result_serie);
 /* Closing connection */
  mysql_close($link);
?>
</BODY>
</HTML>
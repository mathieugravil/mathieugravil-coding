
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
<H1>Ajout d'un album</H1>
<TABLE align="center" width="95%" bgcolor="#fcfcfc">
<TR>
	<form action="enr_album.php" method="post">
<TABLE>

<TR><TD> Nom de l'album:</TD><TD> <Input name="nom_album" type="TEXT" size="30"></TD>
</TR>
<TR><TD> Nom de la série:</TD><TD><SELECT NAME="serie">

<option>
<?php 
 $query_serie = "SELECT * FROM serie ORDER BY nom";
 $result_serie = mysql_query($query_serie) or die("La requête série a
 echouée");
while ($row = mysql_fetch_array($result_serie, MYSQL_NUM))
         {

print"<OPTION>";
printf("%s",  $row[1] );
print"</OPTION>";
	 } ?></SELECT> </TD>
</TR>
<TR><TD> Place dans la série:</TD><TD> <Input name="place" type="int" size="2"></TD>
</TR>
<TR><TD> Nom du dessinateur:</TD><TD><SELECT NAME="dessinateur">

<option>
<?php 
 $query_dessinateur = "SELECT * FROM dessinateur ORDER BY nom";
 $result_dessinateur = mysql_query($query_dessinateur) or die("La requête dessinateur a échouée");
while ($row_dessinateur = mysql_fetch_array($result_dessinateur, MYSQL_NUM))
         {

print"<OPTION>";
printf(" %s",  $row_dessinateur[1] );
print"</OPTION>";
	 } ?></SELECT> </TD>
</TR>
<TR><TD> Nom du scénariste:</TD><TD><SELECT NAME="scenariste">

<option>
<?php 
 $query_scenariste = "SELECT * FROM scenariste ORDER BY nom";
 $result_scenariste = mysql_query($query_scenariste) or die("La
 requête scénariste a échouée");
while ($row_scenariste = mysql_fetch_array($result_scenariste, MYSQL_NUM))
         {

print"<OPTION>";
printf(" %s",  $row_scenariste[1] );
print"</OPTION>";
	 } ?></SELECT> </TD>
</TR>
<TR><TD> Noms de la maison d\édition et de la  collection:</TD><TD><SELECT NAME="maison">

<option>
<?php 
 $query_maison = "SELECT * FROM maison_edition ORDER BY maison, collection";
 $result_maison = mysql_query($query_maison) or die("La
 requête maison a échouée");
while ($row_maison = mysql_fetch_array($result_maison, MYSQL_NUM))
         {

print"<OPTION>";
printf("%s#%s# ",  $row_maison[1],$row_maison[2] );
print"</OPTION>";
	 } ?></SELECT> </TD>
</TR>
<TR><TD> Lien internet:</TD><TD> <Input name="url" type="TEXT" size="30"></TD>
</TR>
<TR>
<TD> <INPUT TYPE="RESET" VALUE="Effacer"></TD><TD><INPUT TYPE="SUBMIT" VALUE="Enregistrer"></TD>
</TR>
</TABLE>
<?php
/* Free resultset */
 mysql_free_result($result_maison);
 mysql_free_result($result_serie);
 mysql_free_result($result_dessinateur);
 mysql_free_result($result_scenariste);
 /* Closing connection */
  mysql_close($link);
?>
</BODY>
</HTML>

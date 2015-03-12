
<?php
/* Connecting, selecting database */
   define('PUN_ROOT', './');
require PUN_ROOT.'config2.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);
 ?>

<HTML>
<HEAD>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<TITLE></TITLE>
</HEAD>

<H1>Saisie de séances</H1>
<TABLE align="center" width="95%" bgcolor="#fcfcfc">
<TR>
	<form action="enr_seance.php" method="post">
<TABLE>

<TR><TD> Nom de la séance:</TD><TD> <Input name="seance_name" type="TEXT" size="30"></TD>
</TR>
<TR><TD> Sport:</TD><TD><SELECT NAME="sport_id">

<option>
<?php 
 $query_sport = "SELECT * FROM sport_type ORDER BY sport_name";
 $result_sport = mysql_query($query_sport) or die("La requête sport a
 echouée");
while ($row = mysql_fetch_array($result_sport, MYSQL_NUM))
         {

printf("<OPTION value=%d>",$row[0]);
printf("%s",  $row[1] );
print"</OPTION>";
	 } ?></SELECT> </TD>
</TR>
<TR><TD> Date:</TD><TD> 
<?php
printf("<Input name=\"date\" type=\"date\" value=%s size=\"8\"></TD>",date("Y/m/d"));
?>
</TR>
<TR><TD> Durée:</TD><TD> <Input name="duration" type="time" size="8"></TD>
</TR>
<TR><TD> Lower:</TD><TD> <Input name="lower" type="int" size="8"></TD>
</TR>
<TR><TD> Upper:</TD><TD> <Input name="upper" type="int" size="8"></TD>
</TR>
<TR><TD> Above:</TD><TD> <Input name="above" type="time" size="8"></TD>
</TR>
<TR><TD> Below:</TD><TD> <Input name="below" type="time" size="8"></TD>
</TR>
<TR><TD> In zone:</TD><TD> <Input name="in_zone" type="time" size="8"></TD>
</TR>
<TR><TD> Fmax:</TD><TD> <Input name="fmax" type="int" size="8"></TD>
</TR>
<TR><TD> Fmoy:</TD><TD> <Input name="fmoy" type="int" size="8"></TD>
</TR>
<TR><TD> Calorie:</TD><TD><Input name="cal" type="int" size="5"> </TD>
</TR>
<TR><TD> % gras:</TD><TD> <Input name="fat" type="int" size="2"></TD>
</TR>
<TR><TD> Distance:</TD><TD><Input name="dist" type="int" size="5"></TD>
</TR>
<TR><TD> Vmoy:</TD><TD> <Input name="vmoy" type="int" size="8"></TD>
</TR>
<TR><TD> Vmax:</TD><TD> <Input name="vmax" type="int" size="8"></TD>
</TR>
<TR>
<TD> <INPUT TYPE="RESET" VALUE="Effacer"></TD><TD><INPUT TYPE="SUBMIT" VALUE="Enregistrer"></TD>
</TR>
</TABLE>
<?php
/* Free resultset */
 mysql_free_result($result_sport);
 /* Closing connection */
  mysql_close($link);
?>
</BODY>
</HTML>

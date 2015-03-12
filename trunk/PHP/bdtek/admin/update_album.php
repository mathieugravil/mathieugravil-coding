<?php
/* Connecting, selecting database */
    define('PUN_ROOT', '../');
require PUN_ROOT.'config.php';
require PUN_ROOT.'include/fonctions.php';
$link=connect_db($db_host, $db_username, $db_password, $db_name);
 $url='';
if (isset($_POST['album']))
{
$album_id=$_POST['album'];
}
if (isset($_POST['nom']))
{
$nom=$_POST['nom'];
}
if (isset($_POST['serie']))
{
$serie=$_POST['serie'] ;
}
if (isset($_POST['genre']))
{
$genre=$_POST['genre'] ;
}
if (isset($_POST['maison']))
{
	if (isset($_POST['collection']))
{
	$m=$_POST['maison'];
	$c=$_POST['collection']; 
$maison="$m#$c#";
}
}
if (isset($_POST['scenariste']))
{
$scenariste=$_POST['scenariste'];
}
if (isset($_POST['dessinateur']))
{
$dessinateur=$_POST['dessinateur'];
}
if (isset($_POST['url']))
{
$url=$_POST['url'];
}
/*
echo "$nom\n";print"$serie\n";
 echo "$maison\n";
echo "$scenariste\n";
echo "$dessinateur\n";
echo "$album_id\n";

*/
?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">

<title></title>
</head>

<body bgcolor='#f4d7b7'>

<H1>Modification d' un album</H1>

<TABLE align="center" width="95%" bgcolor="#fcfcfc">
<TR>
<form action="mod_alb.php" method="post">
<TABLE>

<TR><TD> Nom de l' album:</TD><TD> 
<?php
printf("<Input name=\"nom_album\" type=\"TEXT\" value=\"$_POST[nom]\" size=\"30\">");
printf("<INPUT TYPE=\"hidden\" NAME=\"album_id\" value=\"$album_id\">");
?>
</TD>
</TR>
<TR><TD> Nom de la série:</TD><TD><SELECT NAME="serie">

<option>
<?php 
$query_serie = "SELECT * FROM serie ORDER BY nom";
$result_serie = mysql_query($query_serie) or die("La requête série a
echouée");
while ($row = mysql_fetch_array($result_serie, MYSQL_NUM))
{
	if ($row[1] == $serie)
	{
	print"<OPTION SELECTED='SELECTED'>";
	}
	else{
	print"<OPTION>";
	}
	printf("%s",  $row[1] );
	print"</OPTION>";
} 
?>
</SELECT> </TD>
</TR>
<TR><TD> Place dans la série:</TD><TD> 
<?php
printf("<Input name=\"place\" type=\"int\" value=\"$_POST[place]\" size=\"2\"></TD>");
?>
</TR>
<TR><TD> Nom du dessinateur:</TD><TD>
<?php  
printf("<SELECT NAME=\"dessinateur\" >");
?>

<option>
<?php 
$query_dessinateur = "SELECT * FROM dessinateur ORDER BY nom";
$result_dessinateur = mysql_query($query_dessinateur) or die("La requête dessinateur a échouée");
while ($row_dessinateur = mysql_fetch_array($result_dessinateur, MYSQL_NUM))
{
	if ($row_dessinateur[1] == $dessinateur)
	{
	print"<OPTION SELECTED='SELECTED'>";
	}
	else{
	print"<OPTION>";
	}
	printf(" %s",  $row_dessinateur[1] );
	print"</OPTION>";
}
 ?></SELECT> </TD>
</TR>
<TR><TD> 
Nom du scénariste:</TD><TD>

<?php  
printf("<SELECT NAME=\"scenariste\" >");
?>

<option>
<?php 
$query_scenariste = "SELECT * FROM scenariste ORDER BY nom";
$result_scenariste = mysql_query($query_scenariste) or die("La
requête scénariste a échouée");
while ($row_scenariste = mysql_fetch_array($result_scenariste, MYSQL_NUM))
{
	if ($row_scenariste[1] == $scenariste)
	{
	print"<OPTION SELECTED='SELECTED'>";
	}
	else{
	print"<OPTION>";
	}
	printf(" %s",  $row_scenariste[1] );
	print"</OPTION>";
} ?></SELECT> </TD>
</TR>
<TR><TD> Noms de la maison d édition et de la  collection:</TD><TD>

<?php  
printf("<SELECT NAME=\"maison_ed\" >");
?>

<option>
<?php 
$query_maison = "SELECT * FROM maison_edition ORDER BY maison, collection";
$result_maison = mysql_query($query_maison) or die("La
requête maison a échouée");
while ($row_maison = mysql_fetch_array($result_maison, MYSQL_NUM))
{
	if ($row_maison[1] == $_POST[maison] and $row_maison[2] == $_POST[collection] )
	{
	print"<OPTION SELECTED='SELECTED'>";
	}
	else{
	print"<OPTION>";
	}
	printf("%s#%s# ",  $row_maison[1],$row_maison[2] );
	print"</OPTION>";
} ?></SELECT> </TD>
</TR>
<TR><TD>Genre:</TD><TD>

<?php  
printf("<SELECT NAME=\"genre\" >");
?>

<option>
<?php 
$query_genre = "SELECT * FROM genre ORDER BY genre";
$result_genre = mysql_query($query_genre) or die("La
requête genre a échouée");
while ($row_genre = mysql_fetch_array($result_genre, MYSQL_NUM))
{
	if ($row_genre[1] == $genre)
	{
	print"<OPTION SELECTED='SELECTED'>";
	}
	else{
	print"<OPTION>";
	}
	printf(" %s",  $row_genre[1] );
	
	print"</OPTION>";
} 
print"<OPTION></OPTION>";
?></SELECT> </TD>
</TR>
<TR><TD> Lien internet:</TD><TD> 
<?php
printf("<Input name=\"url\" type=\"TEXT\" value=\"$url\" size=\"30\">");
?>
</TD>
</TR>
<TR>
<TD> <INPUT TYPE="SUBMIT" NAME="DEL" VALUE="Supprimer"></TD><TD><INPUT TYPE="SUBMIT" NAME="DEL" VALUE="Modifier"></TD>
</TR>
</TABLE>

</body>
</html>
<?php
	/* Closing connection */
	mysql_close($link);

?>
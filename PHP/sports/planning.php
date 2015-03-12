<html>
  <head>
<meta http-equiv="Content-Language" content="fr">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	<link rel="stylesheet" href="soirees/style.css" type="text/css">

<link rel="SHORTCUT ICON" href="ude.bmp">
<meta name="description"  lang="fr" content="Site personnel de  Mathieu Gravil (ensta 2004) portant sur la montagne, la   grimpe, le  dessin, image...">
<meta name="keywords"  lang="fr" content="Mathieu Gravil (ensta 2004) montagne  grimpe  image dessin">

  </head>
<body bgcolor="black">

<?php



/*##################### POUR AFFICHER LE CALENDRIER ####################*/


//============================fin des fonctions=============================
define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/mginclude.php';

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);
//print_r($pun_user) ;


if ($pun_user['username'] == 'Invité')
{
echo "<font color=white>Faut être inscrit pour pouvoir voir!!!</font>";
}
else
{
		echo $TZ;
	$user=$pun_user['username'];
					$num_topics='1';
					$num_posts='1';
					$forum_id='9';
					$new_pid ='45';
					$num_replies='1';
					$tid='14';
$jour = $_GET['jour'];
$printweek =  $_GET['printweek'];
$mois = $_GET['mois'];
$annee = $_GET['annee'];
$heure = $_GET['heure'];
$minute = $_GET['minute'];
$type = $_GET['type'];
$user_id = $pun_user['id'];
$aryDateMaintenant = explode("-",date("d-m-Y"));
if(empty($annee)) $annee = $aryDateMaintenant[2];
if(empty($mois)) $mois = $aryDateMaintenant[1];
if(empty($jour)) 
{
$jour = $aryDateMaintenant[0];
}
else
{
$jouractif = $jour  ;
}
if(empty($printweek)) $printweek = numero_semaine($jour , $mois , $annee);
//for ($i = 1 ; $i <=14 ; $i ++) echo "<br><font color=white>demi journée $i : $_POST[$i]</font>";
$der=0;
if ($_POST[1] |$_POST[2] |$_POST[3] |$_POST[4] |$_POST[5] |$_POST[6] |$_POST[7] |
$_POST[8] |$_POST[9] |$_POST[10] |$_POST[11] |$_POST[12] |$_POST[13] |$_POST[14] )
{
//	echo "ok test 1";
$planning_avant = $db->query('SELECT  demijour, act_id
						FROM  planning 
						WHERE annee ='. $annee.'
						AND semaine ='.$printweek.'
						AND user_id ='.$user_id.'
						ORDER BY  demijour, act_id ') or error('Unable to fetch planning avant');

while ($user_data_avant = $db->fetch_assoc($planning_avant))	
	{
			
					
					
		for($i= $der+1 ; $i < $user_data_avant[demijour] ; $i++)
		{
			if ($_POST[$i] != D)
			{
				$insert=$db->query('INSERT INTO planning (annee, semaine, demijour,user_id,act_id) 
												VALUES('.$annee.','.$printweek.','.$i.','.$user_id.','.$_POST[$i].')')or error('Unable to insert B');
		
				
			}	
			
		}
		$der = $user_data_avant[demijour];
		if ($user_data_avant[act_id] != $_POST[$der])
		{
			if($_POST[$der] !=D)
			{
			
					$update = $db->query('UPDATE planning 
									  SET act_id ='.$_POST[$i].'
									  WHERE user_id ='.$user_id.'
									  AND annee ='.$annee.'
									  AND semaine ='.$printweek.'
									  AND demijour ='.$i.'')or error('Unable to update');				
	
			}
			else
			{
							$delete = $db->query('DELETE FROM planning 
											WHERE user_id ='.$user_id.'
			   								AND annee = '. $annee.'
										AND semaine ='.$printweek.'
									AND demijour ='.$i.'') or error('Unable to delete');

			}
		}

	}
for($i=$der+1;$i<=14;$i++)
{
	
if ($_POST[$i] != D)
			{
						$insert=$db->query('INSERT INTO planning (annee, semaine, demijour,user_id,act_id) 
												VALUES('.$annee.','.$printweek.','.$i.','.$user_id.','.$_POST[$i].')')or error('Unable to insert B');
							}	
	
}
		
 zupdate_forum($forum_id,$num_replies,$new_pid,$user,$tid);
			
}		
		
		
//echo"<table width=\"100%\" ><tr><td>&nbsp</td>\n</tr>\n</table>";
echo "<table width=\"100%\" ><tr><th bgcolor=\"#CCCCCC\" colspan= \"6\"><div align=\"center\"> $annee </div></th></tr>\n</table>";


// INITIALISATIONS
// premier jour du mois (lundi, mardi, mercredi etc..)
$intPremierJour = premier_jour_du_mois($mois,$annee);
// nombre de jours dans le mois (28,...,31)
$intNbJoursDansMois = nb_jours_dans_mois($mois,$annee);
// tableau des mois
$aryMois = array(1 => "Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet",
"Ao&ucirc;t","Septembre","Octobre","Novembre","D&eacute;cembre");

// AFFICHAGE DE LA TABLE
echo "<table  width=\"100%\" >\n";
// affichage de la barre de navigation des mois (ex: "< septembre >")
echo "<tr><td>";
if(($mois <= $aryDateMaintenant[1]) and ($annee <= $aryDateMaintenant[2]))
{
 echo "<font color=white><</font>";
}else{
 if(intval($mois) == 1)
 {
 $week = numero_semaine(1,12,$annee-1);
  echo "<a href=\"$PHP_SELF?annee=".($annee - 1)."&mois=12&jour=".($jour)."&printweek=".($week)."\"><font color=\"white\"><</font></a>";
 
 }
 else
 {
  $week = numero_semaine(1,$mois-1,$annee);
  echo "<a href=\"$PHP_SELF?annee=$annee&mois=".($mois - 1)."&jour=".($jour)."&printweek=".($week)."\"><font color=\"white\"><</font></a>";
 }
}
echo "</td>\n<td colspan=6 align=center>";
echo "<font color=\"white\">";
echo $aryMois[intval($mois)];
echo "</font>";
echo "</td>\n<td align=right>";


if(intval($mois) == 12)
 {
 	$week = numero_semaine(1,1,$annee+1);
 echo "<a href=\"$PHP_SELF?annee=".($annee + 1)."&mois=1&jour=".($jour)."&printweek=".($week)."\"><font color=\"white\">></font></a>";
 }
else
{
 $week = numero_semaine(1,$mois+1,$annee);
 echo "<a href=\"$PHP_SELF?annee=".($annee)."&mois=".($mois + 1)."&jour=".($jour)."&printweek=".($week)."\"><font color=\"white\">></font></a>";
}
echo "</td>\n</tr>\n\n";
// affichage de la ligne des jours
echo "<tr align=center>
<td ><font color=\"white\">N° Semaine</font></td>\n
<td align=center><font color=\"white\">Lundi</font></td>\n
<td align=center><font color=\"white\">Mardi</font></td>\n
<td align=center><font color=\"white\">Mercredi</font></td>\n
<td align=center><font color=\"white\">Jeudi</font></td>\n
<td align=center><font color=\"white\">Vendredi</font></td>\n
<td align=center><font color=\"white\">Samedi</font></td>\n
<td align=center><font color=\"white\">Dimanche</font></td>\n</tr>\n\n";
// affichage du reste du calendrier


// on affiche les premiers jours du mois précedent ...
if ($mois > 1)
{
$NbJoursMoisavant = nb_jours_dans_mois($mois-1,$annee);
}
else
{
$NbJoursMoisavant = nb_jours_dans_mois(12,$annee-1);
}
$week = numero_semaine (1 ,$mois , $annee);
if ($week == $printweek ){
echo "<tr BGCOLOR=\"green\" >";
echo "<td align=center><font color=white><b>$week </b>  </font></td>\n";
if (empty($jouractif)) $jouractif = $NbJoursMoisavant - $intPremierJour ;
}
else
{
if ( $intPremierJour ==1 ) $jour =  1 ;
else $jour = $NbJoursMoisavant - $intPremierJour + 2  ;

echo "<td align=center><font color=white><a href=\"$PHP_SELF?annee=$annee&mois=$mois&jour=".($jour)."&printweek=$week\">$week </a>  </font></td>\n";
}
for( $i = $intPremierJour-1; $i > 0 ; $i --)

 echo "<td align=center><font color=blue>".($NbJoursMoisavant + 1 - $i)."</font></td>\n";
// ... pour commencer a partir de notre premier jour calcule plus haut
$j = $intPremierJour;
for( $i=1 ; $i <= $intNbJoursDansMois ; $i ++ )
{
 echo "<td align=center>";
 if($i == intval($aryDateMaintenant[0]))
	   echo "<font color=white><u>$i</u><font>";
 else
  echo "<font color=white>$i</font>";
 echo "</td>\n";
 // dernier jour de la semaine
 if($j == 7)
 {
  // on change de ligne
  if($i < $intNbJoursDansMois)
  {
   echo "</tr>\n\n";

$week = numero_semaine($i+1 , $mois , $annee);
if ($week == $printweek )
{
echo "<tr BGCOLOR=\"green\" >";
echo "<td align=center><font color=white><b>$week </b>  </font></td>\n";
if (empty($jouractif)) $jouractif = $i+1 ;
}else{
echo "<tr>";
$jour = $i + 1 ;
echo "<td align=center><font color=white><a href=\"$PHP_SELF?annee=$annee&mois=$mois&jour=$jour&printweek=$week\">$week </a>  </font></td>\n";
}
   $j = 1;
  }
 // sinon on avance d'un jour dans la semaine...
 }
 else{
  $j ++;
 }
}
$k = 1 ;
// il reste a affiche les jours vides restant
if ($j !=7)
{
for( $i = $j ; $i <= 7 ; $i ++)
 {
 echo "<td align=center><font color=blue> $k </font>  </td>\n";
 $k ++ ;
 }
}
// fin de la table
echo "</tr>\n\n";
echo "</table>\n";
echo "<table  border width=\"100%\"><tr><td colspan=16 BGCOLOR=\"green\" align=center> <b>Semaine n° $printweek </b></td>\n</tr>\n";
//</table>\n";
//echo "<table Border width=\"100% \"><tr>\n";
echo "<td align=center> &nbsp;</font></td>\n\n";
//echo "<td colspan=2 align=center> <font color=white>Lundi $jouractif </font></td>\n\n";
echo "<td colspan=2 align=center> <font color=white>Lundi  </font></td>\n\n";
echo "<td colspan=2 align=center> <font color=white>Mardi </font></td>\n\n";
echo "<td colspan=2 align=center> <font color=white>Mercredi </font></td>\n\n";
echo "<td colspan=2 align=center> <font color=white>Jeudi </font></td>\n\n";
echo "<td colspan=2 align=center> <font color=white>Vendredi </font></td>\n\n";
echo "<td colspan=2 align=center> <font color=white>Samedi </font></td>\n\n";
echo "<td colspan=2 align=center> <font color=white>Dimanche </font></td>\n\n";
echo "</tr>\n<tr>\n";
echo "<td  align=center> <font color=white>&nbsp;</font></td>\n\n";
echo "<td  align=center> <font color=white>Matin</font></td>\n\n";
echo "<td  align=center> <font color=white>Après midi </font></td>\n\n";
echo "<td  align=center> <font color=white>Matin</font></td>\n\n";
echo "<td  align=center> <font color=white>Après midi </font></td>\n\n";
echo "<td  align=center> <font color=white>Matin</font></td>\n\n";
echo "<td  align=center> <font color=white>Après midi </font></td>\n\n";
echo "<td  align=center> <font color=white>Matin</font></td>\n\n";
echo "<td  align=center> <font color=white>Après midi </font></td>\n\n";
echo "<td  align=center> <font color=white>Matin</font></td>\n\n";
echo "<td  align=center> <font color=white>Après midi </font></td>\n\n";
echo "<td  align=center> <font color=white>Matin</font></td>\n\n";
echo "<td  align=center> <font color=white>Après midi </font></td>\n\n";
echo "<td  align=center> <font color=white>Matin</font></td>\n\n";
echo "<td  align=center> <font color=white>Après midi </font></td>\n\n";
echo "<td align=center> &nbsp;</font></td>\n\n";
echo "</tr>\n";
					
$activite = $db->query('SELECT act_id, text	
					FROM activite 
					ORDER BY act_id ') or error('Unable to fetch activite ');
$liste = "<option VALUE=D></option>";
while ($row_activite = $db->fetch_assoc($activite))
	{
		$liste = $liste."<option VALUE=".$row_activite[act_id].">".$row_activite[text]."</option>";
	}
	$liste = $liste."</SELECT>";
$planning = $db->query(' SELECT id, username, demijour, activite.act_id, activite.text
						FROM fluxbb_users
						LEFT JOIN `planning` ON id = user_id
						JOIN activite ON planning.act_id = activite.act_id
						WHERE semaine ='.$printweek.'
						AND annee ='. $annee.'
						AND id >1
						ORDER BY username, demijour, act_id ') or error('Unable to fetch planning ');
$date_user_connecte = 0 ;
if ($db->num_rows($planning))	
{
$username = '';
$dernierdemijouracivité = 0;
echo "<form action=\"planning.php?annee=$annee&mois=$mois&jour=$jour&printweek=$printweek\" method=\"post\">";
			printf("<INPUT TYPE=\"hidden\" NAME=\"user_id\" value=\"$pun_user[id]\">");
while ($user_data = $db->fetch_assoc($planning))	
{		
	if ($username== '') $username = $user_data[username];
	
	if ($dernierdemijouracivité == 0) 
		echo "<tr> <td align=center> <font color=white>$user_data[username]</font></td>\n";
	if ( $user_data[username] == $pun_user['username'])
		{
			//if ($date_user_connecte == 0)
			//{
		//		echo "<form action=\"planning.php?annee=$annee&mois=$mois&jour=$jour&printweek=$printweek\" method=\"post\">";
		//	printf("<INPUT TYPE=\"hidden\" NAME=\"user_id\" value=\"$pun_user[id]\">");
		//	}
			$date_user_connecte = 1 ;
		// Si on est sur le même user 
		if ($user_data[username] == $username)
			{
			//Si la première apparition de cet user on remplit les colonnes avec vide jusqu'a la demi journée d'activité
			for ($i = $dernierdemijouracivité+1 ; $i < $user_data[demijour] ; $i++ )
				{
				echo "<td>";
				echo "<SELECT NAME=\"$i\" >";
			
				echo $liste;
				echo "</td>\n";
				}
				echo "<td align=center bgcolor=\"green\">";
				//echo " <font color=white> $user_data[text] </font>";
			    echo "<SELECT NAME=\"$i\" >";
			    echo "<option value=\"$user_data[act_id]\" selected>$user_data[text]</option>";
			 //   echo "<option value=\"$user_data[act_id]\" selected>$user_data[text]</option>";
				echo $liste;
				echo "</td>\n";
				$dernierdemijouracivité = $user_data[demijour];
			}
		else
			{
			for ($i = $dernierdemijouracivité+1 ; $i <=  14 ; $i ++)
				{
				echo "<td >&nbsp;</td>\n";
				}
				$date_user_connecte = 1 ;
			echo "<td><font color=white>$username</font></td>\n</tr>\n";
			
			echo "<tr> <td align=center> <font color=white>$user_data[username]</font></td>\n";
			$dernierdemijouracivité = 0;	
			$username = $user_data[username];
			

			for ($i = $dernierdemijouracivité+1 ; $i < $user_data[demijour] ; $i++ )
				{
				echo "<td>";
				echo "<SELECT NAME=\"$i\" >";
			
				echo $liste;
				echo "</td>\n";
				}
				echo "<td align=center bgcolor=\"green\"> <font color=white>";
				//echo "$user_data[text] \n";
				//echo "Changer:";
				echo "<SELECT NAME=\"$i\" >";
				echo "<option value=\"$user_data[act_id]\" selected>$user_data[text]</option>";
				echo "$liste</font></td>\n";				
				$dernierdemijouracivité = $user_data[demijour];
			}
		}
	else
		{
		if ($user_data[username] == $username)
			{
			//Si la première apparition de cet user on remplit les colonnes avec vide jusqu'a la demi journée d'activité
			for ($i = $dernierdemijouracivité+1 ; $i < $user_data[demijour] ; $i++ )
				{
				echo "<td>&nbsp;</td>\n";
				}
				echo "<td align=center bgcolor=\"green\"> <font color=white>";
				echo "$user_data[text] </font></td>\n";
				$dernierdemijouracivité = $user_data[demijour];
			}
		else
			{
			if ($username != $pun_user['username'])
			{
			// Si on change de user, il faut compléter la ligne de l'ancien user 
			for ($i = $dernierdemijouracivité+1 ; $i <= 14 ; $i ++)
				{
				echo "<td >&nbsp;</td>\n";
				}
			echo "<td><font color=white>$username</font></td>\n</tr>\n";
			}
			else
			{
			// Si on change de user, il faut compléter la ligne de l'ancien user 
			for ($i = $dernierdemijouracivité+1 ; $i <= 14 ; $i ++)
				{
				echo "<td>";
				echo "<SELECT NAME=\"$i\" >";
				echo $liste;
				echo "</td>\n";
				}
			echo "<td><INPUT TYPE=\"SUBMIT\" VALUE=\"Save\"></td>\n";
			echo "</tr>\n";
			}
			$dernierdemijouracivité = 0;	
			$username = $user_data[username];
			echo "<tr> <td align=center> <font color=white>$user_data[username]</font></td>\n";
			for ($i = $dernierdemijouracivité +1 ; $i < $user_data[demijour] ; $i++ )
				{
				echo "<td>&nbsp;</td>\n";
				}
				echo "<td align=center bgcolor=\"green\"> <font color=white> $user_data[text]</font></td>\n";
				$dernierdemijouracivité = $user_data[demijour];
			}
	
		}		
}

if ( $pun_user[username] == $username )
		{
			for ($i = $dernierdemijouracivité+1 ; $i <= 14 ; $i ++)
				{
				echo "<td>";
				echo "<SELECT NAME=\"$i\" >";
				
				echo $liste;
				echo "</td>\n";
				}
			echo "<td><INPUT TYPE=\"SUBMIT\" VALUE=\"Save\"></td>\n";
			echo "</tr>\n";
		}
	else
		{

		for ($i = $dernierdemijouracivité+1 ; $i <= 14 ; $i ++)
				{
				echo "<td>&nbsp;</td>\n";
				}
			echo "<td><font color=white>$username</font></td>\n</tr>\n";
		}
}	
	if ($date_user_connecte  == 0)
		{
			echo "<form action=\"planning.php?annee=$annee&mois=$mois&jour=$jour&printweek=$printweek\" method=\"post\">";
			printf("<INPUT TYPE=\"hidden\" NAME=\"user_id\" value=\"$pun_user[id]\">");
			echo "<tr> <td align=center> <font color=white>$pun_user[username]</font></td>\n";
			for ($i = 1 ; $i <= 14 ; $i ++)
				{
				echo "<td>";
				echo "<SELECT NAME=\"$i\" >";
			
				echo $liste;
				echo "</td>\n";
				}
			echo "<td><INPUT TYPE=\"SUBMIT\" VALUE=\"Save\"></td>\n";
			echo "</tr>\n";

		}								
echo "</table>";
}
?>
<a href="."><font color=white> RETOUR AU FORUM </font>
</body>
</html>
<?PHP

	#formulaire d'ajout et de modification
	#des users !



	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	include ('../config/databases.php');	// fichiers de configuration des bases de donn�es
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

?>

<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>

<script type="text/javascript"> 
	
	// v�rouille l'acc�s au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit = document.getElementById("post_user");
		var user_nom = document.getElementById("nom").value;
		var user_login = document.getElementById("login").value;
		var user_password = document.getElementById("password").value;
		
		if (user_nom == "" || user_login == "" || user_password == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	
	// ferme la smoothbox et rafraichis la page
	function refresh_quit () {
		// lance la fonction avec un d�lais de 1000ms
		window.setTimeout("HTML_AJAX.replace('conteneur', 'gestion_utilisateurs/voir_utilisateurs.php');", 1000);
		TB_remove();
	}
	
</script>

<?PHP

	// adresse de connexion � la base de donn�es
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	$id = $_GET['id'];


	
	#***************************************************************************
	# 				CREATION de l'utilisateur
	#***************************************************************************
	
	
	if ( $id == '-1' ) {	// Formulaire vierge de cr�ation
	
		echo "<h2>formulaire de cr�ation d'un nouvel utilisateur</h2><br>";
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>

		<form onsubmit="return !HTML_AJAX.formSubmit(this,'target');" action="gestion_utilisateurs/post_utilisateurs.php?action=add" method="post" name="frmTest" id="frmTest">

			<center>
			<table width=500>
			
				<tr>
					<TD>Nom *</TD>
					<TD><input type=text name=nom id=nom onkeyup="validation();"/></TD>
				</tr>
				
				<tr>
					<TD>Login *</TD>
					<TD><input type=text name=login id=login onkeyup="validation();" /></TD>
				</tr>
				
				<tr>
					<TD>Password *</TD> 
					<TD><input type=password name=password id=password onkeyup="validation();"	/></TD>
				</tr>
				
				<tr>
					<TD>Mail</TD> 
					<TD><input type=text name=mail 	/></TD>
				</tr>
				
				<tr>
					<TD>Niveau</TD>
					<TD><select name="niveau" size="1">
							<option value=1>ATI</option>
							<option value=2>TICE</option>
							<option value=3>PROF</option>
							<option value=9>Autre...</option>
						</select>
					</TD>
				</tr>
				
				<tr>
					<td>Skin</td>
					<td>
						<select name="skin">
						<?PHP
							$dossier = opendir("../skins");
							while ( $skin = readdir($dossier) ) {
								if ( $skin != "." && $skin != ".." && $skin != ".svn") {
									echo "<option value=$skin>$skin</option>";
								}
							}
							closedir($dossier);
						?>
						</select>
					</td>
				</tr>
				
				<tr>
					<TD>Page de D�marrage</TD>
					<TD><select name="page" size="1">
							<option value="gestion_inventaire/voir_materiels.php">mat�riels</option>
							<option value="modules/stats/csschart.php">stats</option>
							<option value="modules/rss/rss.php">rss</option>
							<option value="gestion_demandes/voir_demandes.php">demandes</option>
							<option value="gestion_demandes/voir_interventions.php">interventions</option>
							<option value="modules/stats/utilisation_parc.php">utilisation parc</option>
							<option value="modules/wol/voir_liste_wol.php">WOL</option>
						</select>
					</TD>
				</tr>
				
			</table>

			<br>
			<input type=submit value='Ajouter utilisateur' onClick="refresh_quit();" id="post_user" disabled>
			
			</center>

		</FORM>
				

		<?PHP
		
		
		
		
		#***************************************************************************
		# 				MODIFICATION de l'utilisateur
		#***************************************************************************
		
		
		
	} else {	// formulaire de modification pr�rempli
	
		echo "<h2>formulaire de modification d'un utilisateur</h2><br>";
		
		// Requete pour r�cup�rer les donn�es des champs pour le user � modifier
		$user_a_modifier = $db_gespac->queryAll ( "SELECT user_id, user_nom, user_logon, user_password, user_niveau, user_mail, user_skin, user_accueil FROM users WHERE user_id=$id" );		
		
		// valeurs � affecter aux champs
		$user_id 			= $user_a_modifier[0][0];
		$user_nom	 		= $user_a_modifier[0][1];
		$user_logon	 		= $user_a_modifier[0][2];
		$user_password 		= $user_a_modifier[0][3];
		$user_niveau	 	= $user_a_modifier[0][4];
		$user_mail 			= $user_a_modifier[0][5];
		$user_skin 			= $user_a_modifier[0][6];
		$user_accueil		= $user_a_modifier[0][7];
	
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>
		
		<form onsubmit="return !HTML_AJAX.formSubmit(this,'target');" action="gestion_utilisateurs/post_utilisateurs.php?action=mod" method="post" name="frmTest" id="frmTest">
			<input type=hidden name=id value=<?PHP echo $user_id;?> >
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom</TD>
					<TD><input type=text name=nom id=nom value= "<?PHP echo $user_nom; ?>" 	/></TD>
				</tr>
				
				<tr>
					<TD>log In</TD>
					<TD><input type=text name=login value= "<?PHP echo $user_logon; ?>"	/></TD>
				</tr>
				
				<tr>
					<TD>Password</TD> 
					<TD><input type=password name=password value= "<?PHP echo $user_password; ?>"	/></TD>
				</tr>
								
				<tr>
					<TD>Mail</TD> 
					<TD><input type=text name=mail value= "<?PHP echo $user_mail; ?>"	/></TD>
				</tr>
				
				<tr>
					<TD>Niveau</TD>
						<TD><select name="niveau" size="1">
							<option <?PHP if ( $user_niveau == 1 ) echo "selected"; ?> value=1>ATI</option>
							<option <?PHP if ( $user_niveau == 2 ) echo "selected"; ?> value=2>TICE</option>
							<option <?PHP if ( $user_niveau == 3 ) echo "selected"; ?> value=3>Professeur</option>
							<option <?PHP if ( $user_niveau == 9 ) echo "selected"; ?> value=9>Autre...</option>
						</select>
					</TD>
				</tr>
				
				<tr>
					<td>Skin</td>
					<td>
						<select name="skin">
						<?PHP
							$dossier = opendir("../skins");
							while ( $skin = readdir($dossier) ) {
								if ( $skin != "." && $skin != ".." && $skin != ".svn") {
									$selected = $skin == $user_skin ? "selected" : "" ;
									echo "<option $selected value=$skin>$skin</option>";
								}
							}
							closedir($dossier);
						?>
						</select>
					</td>
				</tr>
				
				<tr>
					<?PHP 
						$selected = $accueil == $user_accueil ? "selected" : "" ;
					?>
				
					<TD>Page de D�marrage</TD>
					<TD><select name="page" size="1">
							<option value="gestion_inventaire/voir_materiels.php">mat�riels</option>
							<option value="modules/stats/csschart.php">stats</option>
							<option value="modules/rss/rss.php">rss</option>
							<option value="gestion_demandes/voir_demandes.php">demandes</option>
							<option value="gestion_demandes/voir_interventions.php">interventions</option>
							<option value="modules/stats/utilisation_parc.php">utilisation parc</option>
							<option value="modules/wol/voir_liste_wol.php">WOL</option>
						</select>
					</TD>
				</tr>
				
			</table>
			
			<br>
			<input type=submit value='Modifier cet utilisateur' onClick="refresh_quit();" >

			</center>

		</FORM>
		
		<?PHP
	}	
?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>
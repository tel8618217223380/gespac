<?PHP
	session_start();
	
	/* fichier de visualisation des utilisateurs :
	
		view de la db gespac avec tous les users du parc
	*/


	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-12#", $_SESSION['droits']);
	
	
?>

<div class="entetes" id="entete-migusers">	
	<span class="entetes-titre">MIGRATION DES UTILISATEURS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">
		Selectionnez dans les listes déroulantes les professeurs correspondants. Les sûrs à 100%, je les ai déjà selectionnés ;) 
		<br><SPAN style="background-color:green;">VERT</SPAN> : Correspondances sur nom et prénom.
		<br><SPAN style="background-color:yellow;">JAUNE</SPAN> : Correspondances	sur le nom.
		<br><SPAN style="background-color:red;">ROUGE</SPAN> : Aucune correspondance.
	</div>	
</div>

<div class="spacer"></div>


<?PHP 

	// Le fichier migration_users_ad2008.csv existe t'il ?
	
	$handle = fopen("dump/migration_users_ad2008.csv", "r");
	
	if ($handle) {

		// cnx à la base de données GESPAC
		$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

		// Création d'une table temporaire
		$table_temp_drop = $con_gespac->Execute("DROP TABLE table_temp;");
		$table_temp = $con_gespac->Execute("CREATE TABLE table_temp (nom VARCHAR( 255 ) NOT NULL ,prenom VARCHAR( 255 ) NOT NULL ,login VARCHAR( 255 ) NOT NULL ,pass VARCHAR( 255 ) NOT NULL) ENGINE = MYISAM ;");
	
	
		$row = 0;	// [AMELIORATION] penser à virer l'entête
		$csvfile = array();
		
		while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
			
			$line[$row][0] = $data[0];	
			$line[$row][1] = $data[1];			
			$line[$row][2] = $data[2];
			$line[$row][3] = $data[3];

			if ($line[$row][0] <> "NOM" )	{
				$con_gespac->Execute ("INSERT INTO table_temp VALUES ('" . $line[$row][0] . "', '". $line[$row][1] ."', '".$line[$row][2]."', '".$line[$row][3]."') ;");
			}

			$row++;
		}


		$liste_csv = $con_gespac->QueryAll("SELECT nom, prenom, login, pass FROM table_temp;");

		// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
		$liste_des_utilisateurs = $con_gespac->QueryAll ( "SELECT user_id, user_nom, user_logon FROM users WHERE user_logon<>'ati' ORDER BY user_nom" );

	?>
		<form method="POST" action="modules/migration_users/post_migration_users.php" name="post_form" id="formulaire">
		
			<center>
				
			<input type=submit value="migrer les comptes" id="post_form"><br><br>
			
			<table class="bigtable hover" id="migration_users_table">
				<th>Nom</th>
				<th>Logon</th>
				<th>Correspondance</th>
				<th>&nbsp;</th>
						
				<?PHP	

					// On parcourt le tableau
					foreach ( $liste_des_utilisateurs as $record ) {
						
						$indice = "red";
									
							$nom 			= $record['user_nom'];
							$logon 			= $record['user_logon'];
							$id				= $record['user_id'];
									

							echo "<tr id='tr_id$id'>";
							
								echo "<td> $nom </td>";
								echo "<td> $logon </td>";

								
								if ( $E_chk ) {
									
									echo "<td>
										<select name='$id'>";
										
										echo "<option value='inconnu'> INCONNU </option>";
										
										foreach ($liste_csv as $line) {
											
											$nom_complet = $line['nom'] . " " . $line['prenom'];
											$login = $line['login'];
											
											if (strtoupper($nom_complet) == strtoupper($nom) ) {
												$selected = "selected";
												$indice = "green";
											}
											else {
												$selected = "";
											}
											
																						
											if ( $indice == "red" && preg_match ("/" . $line['nom'] . "/i", $nom) ) {
												$indice = "yellow";
												$selected = "selected";
											}
											
											
										
											echo "<option value='$login' $selected > $nom_complet </option>";
										
										}

										echo"</select>								
									</td>";
									
									echo "<td bgcolor=$indice>&nbsp;</td>";
										
								} else {
									echo "<td>&nbsp</td>";
									echo "<td>&nbsp</td>";
								}
							
						echo "</tr>";
					}
				?>		

			</table>
			
			</center>
			
			
		<?PHP
			// On se déconnecte de la db
			$con_gespac->Close();
		
		echo "</form>";
			
	}
	
	else {
	
		echo "Il faut poster (ou reposter ?) le fichier de migration ad2008<br><br><a href='index.php?page=migusers'>CLIQUEZ ICI pour reposter le fichier.</a>";
				
	}
	?>

	
	<script>
	
	$(function() {	
				
		// **************************************************************** POST AJAX FORMULAIRES
		$("#post_form").click(function(event) {

			/* stop form from submitting normally */
			event.preventDefault(); 
			
			if ( validForm() == true) {
			
				// Permet d'avoir les données à envoyer
				var dataString = $("#formulaire").serialize();
				
				// action du formulaire
				var url = $("#formulaire").attr( 'action' );
				
				var request = $.ajax({
					type: "POST",
					url: url,
					data: dataString,
					dataType: "html"
				 });
				 
				 request.done(function(msg) {
					$('#targetback').show(); $('#target').show();
					$('#target').html(msg);
					window.setTimeout("document.location.href='index.php?page=utilisateurs'", 2500);
				 });
			}			 
		});	
	});
	
</script>
	

<?PHP
	
	/* fichier de visualisation des logs des prets :
	
		view de la db gespac avec tous le matos prêté et rendu
		avec possibilité de rééditer une convention
	*/
	
	
?>

<script type="text/javascript">	
	

	// *********************************************************************************
	//
	//				Fonction de validation de la suppression des logs
	//
	// *********************************************************************************
	
	function validation_suppr_logs () {

		var valida = confirm ("La suppression des logs va exécuter un dump automatique dans le fichier DUMP_LOGS.CSV du gestionnaire de fichiers.\n\nMERCI DE VÉRIFIER QUE VOTRE FICHIER DUMP_LOGS.CSV N'EST PAS OUVERT !");
		
		// si la réponse est TRUE ==> on lance la page post_logs.php
		if (valida) {
			$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
			$('target').load("gestion_donnees/post_logs.php");
			window.setTimeout("document.location.href='index.php?page=logs'", 1500);			
		}
	}		
	
	
	
	// *********************************************************************************
	//
	//				Fonction de filtrage des tables
	//
	// *********************************************************************************

	function filter (phrase, _id){

		var words = phrase.value.toLowerCase().split(" ");
		var table = document.getElementById(_id);
		var ele;
		var elements_liste = "";
			
		for (var r = 1; r < table.rows.length; r++){
			
			ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
			var displayStyle = 'none';
			
			for (var i = 0; i < words.length; i++) {
				if (ele.toLowerCase().indexOf(words[i])>=0) {	// la phrase de recherche est reconnue
					displayStyle = '';
				} 
				else {	// on masque les rows qui ne correspondent pas
					displayStyle = 'none';
					break;
				}
			}
			
			// Affichage on / off en fonction de displayStyle
			table.rows[r].style.display = displayStyle;
		}
	}
	
</script>	


<div class="entetes" id="entete-logs">	

	<span class="entetes-titre">LES LOGS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Toutes les opérations importantes laissent une trace dans les logs.<br>Vider les logs créé automatiquement un fichier dans le gestionnaire de fichiers.</div>

	<span class="entetes-options">
		
		<span class="option">		
			<input type=button value="vider les logs" onClick="validation_suppr_logs();">
		</span>
		
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'log_prets_table');" type="text" value=<?PHP echo $_GET['filter'];?>> </form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP
	
	// cnx gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);

	$liste_des_logs = $con_gespac->QueryAll ( "SELECT log_date, log_type, log_texte FROM logs ORDER BY log_date DESC" );	

?>
	
	<table id="log_prets_table" class='tablehover'>
	
		<th>Type</th>
		<th>Date</th>
		<th>Objet du log</th>
	
		<?PHP	

			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_logs as $record ) {
	
				$date 		= $record['log_date'];
				$type 		= $record['log_type'];
				$texte		= urldecode($record['log_texte']);

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";
						
				// couleur operation
				switch ($type) {
					case "Suppression matériel"		: 	$td_color = "#ff7b7b";	break;
					case "Suppression marque"  		: 	$td_color = "#ff7b7c";	break;
					case "Suppression salle"   		: 	$td_color = "#ff7b7d";	break;
					case "Suppression compte"  		: 	$td_color = "#ff7b7e";	break;
					case "Création compte"	   		: 	$td_color = "#b3fffe";	break;
					case "Création salle"	   		: 	$td_color = "#b3ffff";	break;
					case "Création marque"	   		: 	$td_color = "#b3fffd";	break;
					case "Création matériel"   		: 	$td_color = "#b3fffc";	break;
					case "Création collège"		   	: 	$td_color = "#b3fffb";	break;
					case "Création demande"		   	: 	$td_color = "#b3fffa";	break;
					case "Modification compte"	   	: 	$td_color = "#9aff9f";	break;
					case "Modification collège"	   	: 	$td_color = "#9aff9e";	break;
					case "Modification salle"	   	: 	$td_color = "#9aff9d";	break;
					case "Modification matériel"   	: 	$td_color = "#9aff9c";	break;
					case "Modification marque"	   	: 	$td_color = "#9aff9b";	break;
					case "Affectation salle"	   	: 	$td_color = "#ffd20f";	break;
					case "Dump GESPAC"			   	: 	$td_color = "#c6baff";	break;
					case "Dump OCS"				   	: 	$td_color = "#c6bafe";	break;
					case "Dump LOGS"			   	: 	$td_color = "#c6bafd";	break;
					case "Import OCS"			   	: 	$td_color = "#c6bafd";	break;
					case "Import IACA"			   	: 	$td_color = "#c6bafc";	break;
					case "Import CSV"			   	: 	$td_color = "#f1ff73";	break;
					case "Etat demande"			   	: 	$td_color = "#c6bafb";	break;
					case "Prêté"				   	: 	$td_color = "#f1ff73";	break;
					case "Rendu"				   	: 	$td_color = "#2f7bff";	break;
				}
				
					
				echo "<tr class='$tr_class'>";									
					echo "<td bgcolor=$td_color> $type </td>";
					echo "<td>$date</td>";
					echo "<td align=left>$texte</td>";
				echo "</tr>";
				
				$compteur++;				
			}
		?>	
			
	</table>

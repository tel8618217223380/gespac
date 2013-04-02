<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');		
	include_once ('../../class/Log.class.php');		
	
	echo "<h2>formulaire d'ajout de mat�riels en masse par fichier CSV</h2><br>";
	echo "<h3>ATTENTION : Il faut faire UN fichier CSV par mod�le !</h3><br>";

	// cnx � la base de donn�es GESPAC
	$con_gespac	= new Sql($host, $user, $pass, $gespac);
	
	// Requ�te qui va r�cup�rer les origines des dotations ...
	$liste_origines = $con_gespac->QueryAll ( "SELECT origine FROM origines ORDER BY origine" );
	
	// Requ�te qui va r�cup�rer les �tats des mat�riels ...
	$liste_etats = $con_gespac->QueryAll ( "SELECT etat FROM etats ORDER BY etat" );
	
	?>
	
	<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>

<script type="text/javascript"> 
	
		
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
		var nb_resultats = 0;
			
		if (phrase.value == "") {	// Si la phrase est nulle, on masque toutes les lignes
			for (var r = 1; r < table.rows.length; r++)	table.rows[r].style.display = "none";	
		}
		else {			
			for (var r = 1; r < table.rows.length; r++){
				
				ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
				var displayStyle = 'none';
				
				for (var i = 0; i < words.length; i++) {
					if (ele.toLowerCase().indexOf(words[i])>=0) {	// la phrase de recherche est reconnue
						displayStyle = '';
						nb_resultats++;
					}	
					else {	// on masque les rows qui ne correspondent pas
						displayStyle = 'none';
						break;
					}
				}
				
				// Affichage on / off en fonction de displayStyle
				table.rows[r].style.display = displayStyle;	
			}
			
			// Affiche le div "pasderesultat", si jamais il n'y a ... pas de r�sultat !
			if ( nb_resultats == 0 )
				$('pasderesultat').style.display = "";
			else
				$('pasderesultat').style.display = "none";
			
		}
	}	
	
	
		
	// *********************************************************************************
	//
	//			AJOUT d'un MARQUE par sa CORRESPONDANCE
	//
	// *********************************************************************************
	
	function validation_choisir_marque (corr_id, marque) {
			
		var valida = confirm('Voulez-vous vraiment choisir la marque ' + marque + ' ?');
		
		// si la r�ponse est TRUE ==> on colle dans un input la valeur corr_id
		if (valida) {
			$('marque_id').value = corr_id;
			
			$('choix_modele').style.display = 'none';
			$('table_modele_selectionne').style.display = '';
			
			$('modele_selectionne').value = marque;
		}
	}
	
	
	// *********************************************************************************
	//
	//			FAIT REAPPARAITRE LE CHOIX DE SELECTION DE LA MARQUE
	//
	// *********************************************************************************
	
	function choisir_modele () {
		
		$('choix_modele').style.display = '';
		$('table_modele_selectionne').style.display = 'none';
		
		$('marque_id').value = "";
		$('modele_selectionne').value = "";
	}
	
	// ferme la smoothbox et rafraichis la page
	function refresh_quit () {
		// lance la fonction avec un d�lais de 1500ms
		window.setTimeout("$('conteneur').load('gestion_inventaire/voir_materiels.php');", 1500);
		SexyLightbox.close();
	}
	
	
</SCRIPT>
		
<script>
	// Donne le focus au premier champ du formulaire
	$('filt').focus();
</script>
		
<form method="POST" action="gestion_inventaire/post_import_csv.php" target=_blank enctype="multipart/form-data">
	<center>
			
	<table width=400 align=center cellpadding=10px>

		<tr>
			<TD>Origine</TD> 
			<TD>
				<select name="origine">
					<?PHP	foreach ($liste_origines as $origine) {	echo "<option value='" . $origine['origine'] ."'>" . $origine['origine'] ."</option>";	}	?>
				</select>
			</TD>
		</tr>
		
		<tr>
			<TD>Etat du mat�riel</TD>
			<TD>
				<select name="etat">
					<?PHP	foreach ($liste_etats as $etat) {	$selected = $etat['etat'] == "Fonctionnel" ? "selected" : ""; echo "<option $selected value='" . $etat['etat'] ."'>" . $etat['etat'] ."</option>";	}	?>
				</select>
			</TD>
		</tr>
		
	</table>
	
	<br>
	
	<!--

	GESTION PAR CORRESPONDANCE DE L'INSERTION D'UNE MARQUE

	-->
		
	<div id='choix_modele'>
	
		<center>
	
		<table width=400 align=center cellpadding=10px>
			<tr>
				<td>Choisir un mod�le :</td>
				<td><input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'marque_table_csv');" type="text"> </td>
			</tr>
		</table>
	
		<br>
		
		<?PHP
		// ici il faut r�cup�rer les lignes DISTINCTES histoire de ne pas surcharger le tableau
		//$liste_correspondances = $db_gespac->queryAll ( "SELECT corr_id, corr_marque_ocs, corr_type, corr_stype, corr_marque, corr_modele FROM correspondances GROUP BY corr_modele ORDER BY corr_modele" );
		$liste_marques = $con_gespac->QueryAll ( "SELECT marque_id, marque_marque, marque_model, marque_type, marque_stype FROM marques ORDER BY marque_model" );
		?>
		 	 	 	 	
		<!-- s'affiche si il n'y a pas de r�sultat -->
		<div id="pasderesultat" style='display:none'>Pas de r�sultat, vous devez cr�er le mod�le manuellement.</div>
		
		<table id="marque_table_csv" class='tablehover'>

			<?PHP
				foreach ( $liste_marques as $marque ) {
				
					$marque_id 		= $marque['marque_id'];
					$marque_marque 	= $marque['marque_marque'];
					$marque_model 	= $marque['marque_model'];
					$marque_type 	= $marque['marque_type'];
					$marque_stype 	= $marque['marque_stype'];
				
					echo "<tr style='display:none' class='tr_filter'>";
						echo "<td width=200>$marque_type</td>";
						echo "<td width=200>$marque_stype</td>";
						echo "<td width=200>$marque_marque</td>";
						echo "<td width=200>$marque_model</td>";
						echo "<td><a href='#' onclick=\"validation_choisir_marque($marque_id, '$marque_marque $marque_model');\"><img src='img/arrow-right.png' width=16 height=16 title='Choisir ce mod�le'> </a></td>";
					echo "</tr>";
				
				}
			
			?>
			
		</table>
	</div>	
	
	<table width=400 align=center cellpadding=10px style='display:none' id="table_modele_selectionne">
	 	<tr>
			<td>Mod�le s�lectionn� :</td>
			<td><input type=hidden name="marque_id" id="marque_id"> <input type="text" id="modele_selectionne"> </td>
			<td><a href='#' onclick="choisir_modele();">changer</a></td>
		</tr>
	 </table>

	 <br>
	
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	 <center>
	 <table width=400 align=center cellpadding=10px>
		<tr>
			<td>Fichier CSV</td>
			<td><input type="file" name="myfile"></td>
		</tr>
	 </table>
	 </center>



	<br>
	<br>
	<center>
	<input type="submit" name="envoyer" value="Envoyer le fichier" onclick="refresh_quit();">

	</center>

</FORM>

<br>
<br>
<center>
	<a href='#' onclick='alert("Formalisme pour le fichier CSV d`import : \n \"Nom_materiel1\";\"no_serie1\";\"no_dsit1\" \n \"Nom_materiel2\";\"no_serie2\";\"no_dsit\" \n \"Nom_materiel3\";\"no_serie3\";\"no_dsit3\" \n ... ");'>AIDE</a>
</center>
	
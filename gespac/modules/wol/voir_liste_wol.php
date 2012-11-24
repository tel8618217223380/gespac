<?PHP
	
	/* 
		Fichier pour s�lection des machines � r�veiller
	
	*/

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
?>

<script type="text/javascript">
	/******************************************
	*
	*		AJAX
	*
	*******************************************/
	
	window.addEvent('domready', function(){
		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML, filt) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET � POST (en effet, avec GET il r�cup�re la totalit� du tableau get en param�tres et lorsqu'on poste la page formation on d�passe la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('modules/wol/voir_liste_wol.php');", 1500);
				}
			
			}).send(this.toQueryString());
		});			
	});
	
</script>


<!--	DIV target pour Ajax	-->
<div id="target"></div>


<?PHP
	// cnx � gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id as salleid, mat_mac FROM materiels, marques, salles WHERE (materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND mat_mac <> '' ) ORDER BY mat_nom" );
?>
	<h3>R�veil des machines � distance</h3><br>
	
	<span id="nb_selectionnes">[0]</span> machines � r�veiller.
	
	
	<center>
	
	<form name="post_form" id="post_form" action="modules/wol/post_wol.php" method="post">
	
		<!--------------------------------------------	LISTE DES ID A POSTER	------------------------------------------------>
		<input type=hidden name=materiel_a_poster id=materiel_a_poster value=''>	

		<input type=submit id="wakethem" value="R�veiller ces machines" style="display:none"><br>
		
	</form>
	

	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'wol_table', '1')" type="text"></center>
	</form>
	
	
	
	<table class="tablehover" id="wol_table" width=870>
	
		<th> <input type=checkbox id=checkall onclick="checkall('wol_table');" > </th>
		<th>Nom</th>
		<th>Serial</th>
		<th>Etat</th>
		<th>Salle</th>
		<th>MacADD</th>
		
		<?PHP	
			
	
		
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_materiels as $record ) {
				// On �crit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";

				$nom 		= $record['mat_nom'];
				$dsit 		= $record['mat_dsit'];
				$serial 	= $record['mat_serial'];
				$etat 		= $record['mat_etat'];
				$marque		= $record['marque_marque'];
				$model 		= $record['marque_model'];
				$type 		= $record['marque_type'];
				$stype		= $record['marque_stype'];
				$id 		= $record['mat_id'];
				$salle 		= $record['salle_nom'];
				$salle_id 	= $record['salleid'];
				$mac 		= $record['mat_mac'];
			
				
				echo "<tr id=tr_id$id class=$tr_class>";
					/*	chckbox	*/	echo "<td> <input type=checkbox name=chk indexed=true value='$id' onclick=\"select_cette_ligne('$id', $compteur) ; \"> </td>";	
					/*	nom		*/	echo "<td> <a href='gestion_inventaire/voir_fiche_materiel.php?height=500&width=640&mat_nom=$nom' rel='slb_wol' title='Caract�ristiques de $nom'>$nom</a> </td>";
					/*	serial	*/	echo "<td> $serial </td>";
					/*	etat	*/	echo "<td> $etat </td>";
					/*	salle	*/	echo "<td> <a href='gestion_inventaire/voir_membres_salle.php?height=480&width=640&salle_id=$salle_id' rel='slb_wol' title='Membres de la salle $salle'>$salle</a> </td>";
					/*	macaddr	*/	echo "<td> $mac </td>";

				echo "</tr>";
				
				$compteur++;
			}
		?>		
		
	</table>
	</center>
	
	<br>
	
	
<?PHP
	// On se d�connecte de la db
	$con_gespac->Close();
?>


<script type="text/javascript">
	
	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_wol'});
	});

	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";

			
	// *********************************************************************************
	//
	//				Selection/d�selection de toutes les rows
	//
	// *********************************************************************************	
	
	function checkall(_table) {
		var table = document.getElementById(_table);	// le tableau du mat�riel
		var checkall_box = document.getElementById('checkall');	// la checkbox "checkall"
		
		for ( var i = 1 ; i < table.rows.length ; i++ ) {

			var lg = table.rows[i].id					// le tr_id (genre tr115)
			
			if (checkall_box.checked == true) {
				document.getElementsByName("chk")[i - 1].checked = true;	// on coche toutes les checkbox
				select_cette_ligne( lg.substring(5), i, 1 )					//on selectionne la ligne et on ajoute l'index
			} else {
				document.getElementsByName("chk")[i - 1].checked = false;	// on d�coche toutes les checkbox
				select_cette_ligne( lg.substring(5), i, 0 )					//on d�selectionne la ligne et on la retire de l'index
			}
		}
	}
	
	
	// *********************************************************************************
	//
	//				Ajout des index pour postage sur clic de la checkbox
	//
	// *********************************************************************************	
	 
	function select_cette_ligne( tr_id, num_ligne, check ) {

		var chaine_id = document.getElementById('materiel_a_poster').value;
		var table_id = chaine_id.split(";");
		
		var nb_selectionnes = document.getElementById('nb_selectionnes');
		
		var ligne = "tr_id" + tr_id;	//on r�cup�re l'tr_id de la row
		var li = document.getElementById(ligne);	
		
		if ( li.style.display == "" ) {	// si une ligne est masqu�e on ne la selectionne pas (pratique pour le filtre)
		
			switch (check) {
				case 1: // On force la selection si la ligne n'est pas d�j� coch�e
					if ( !table_id.contains(tr_id) ) { // la valeur n'existe pas dans la liste
						table_id.push(tr_id);
						li.className = "selected";
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	// On entre le nombre de machines s�lectionn�es	
					}
				break;
				
				case 0: // On force la d�selection
					table_id.erase(tr_id);
					nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	 // On entre le nombre de machines s�lectionn�es			
					// alternance des couleurs calcul�e avec la parit�
					if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
				break;
				
				
				default:	// le check n'est pas pr�cis�, la fonction d�termine si la ligne est selectionn�e ou pas
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	 // On entre le nombre de machines s�lectionn�es			

						// alternance des couleurs calcul�e avec la parit�
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					
					} else {	// le tr_id n'est pas trouv� dans la liste, on cr�� un nouvel tr_id � la fin du tableau
						table_id.push(tr_id);
						li.className = "selected";
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	// On entre le nombre de machines s�lectionn�es	
					}
				break;			
			}
	
			// on concat�ne tout le tableau dans une chaine de valeurs s�par�es par des ;
			document.getElementById('materiel_a_poster').value = table_id.join(";");
			

			if ( $('materiel_a_poster').value != "" ) 
				$('wakethem').style.display = "";
			else 
				$('wakethem').style.display = "none";
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

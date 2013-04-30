	<!-- 

		Liste des membres d'une MARQUE particulière

	-->


<?PHP

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Log.class.php');	
	include_once ('../../class/Sql.class.php');	

	
	// libellé du type de marque récupéré de la page voir_marques.php
	$marque_marque = $_GET ['marque_marque'];

	// cnx à la base de données GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id FROM materiels, marques WHERE materiels.marque_id = marques.marque_id AND marque_marque='$marque_marque' order by mat_nom" );
	
	if (count($liste_des_materiels) <1 ) {
		echo "<br><h3>Pas de matériel pour cette marque ! </h3>"; exit();
	}
	
	echo "<p><small>" . count($liste_des_materiels) . " matériel(s)</small></p>";
	
	$fp = fopen('../dump/extraction.csv', 'w+');	//Ouverture du fichier
	fputcsv($fp, array('nom', 'dsit', 'serial', 'etat', 'modele', 'type', 'stype'), ',' );	// ENTETES
	echo "<center><a href='./dump/extraction.csv' target=_blank>fichier CSV</a></center><br>";
	
?>


<center>
	
	<table id="myTable" class='alternate smalltable'>
		<th>nom</th>
		<th>dsit</th>
		<th>serial</th>
		<th>etat</th>
		<th>modèle</th>
		<th>type</th>
		<th>s/type</th>
		
		<?PHP	
			
			$compteur = 0;	
			// On parcourt le tableau
			foreach ($liste_des_materiels as $record ) {
				// On écrit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";

				echo "<tr class=$tr_class>";
					$nom 		= $record['mat_nom'];
					$dsit 		= $record['mat_dsit'];
					$serial 	= $record['mat_serial'];
					$etat 		= $record['mat_etat'];
					$marque		= $record['marque_marque'];
					$model 		= $record['marque_model'];
					$type 		= $record['marque_type'];
					$stype 		= $record['marque_stype'];
					$id 		= $record['mat_id'];
					
					//echo "<td> $nom </td>";
					echo "<td> $nom </td>";
					echo "<td> $dsit </td>";
					echo "<td> $serial </td>";
					echo "<td> $etat </td>";
					echo "<td> $model </td>";
					echo "<td> $type </td>";
					echo "<td> $stype </td>";
				echo "</tr>";
				
				// On constitue le fichier CSV de l'extraction
				fputcsv($fp, array($nom, $dsit, $serial, $etat, $model, $type, $stype), ',');
				
				$compteur++;
			}
			
			fclose($fp);
		?>		

	</table>
	
	</center>
	
	<br>
	
<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();

?>

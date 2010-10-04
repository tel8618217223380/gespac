	<!-- 

		Liste des membres d'une MARQUE particuli�re

	-->


<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	include ('../config/databases.php');	// fichiers de configuration des bases de donn�es
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

	
	// libell� du type de marque r�cup�r� de la page voir_marques.php
	$marque_marque = $_GET ['marque_marque'];

	
	// adresse de connexion � la base de donn�es
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	

	// stockage des lignes retourn�es par sql dans un tableau nomm� avec originalit� "array" (mais "tableau" peut aussi marcher)
	$liste_des_materiels = $db_gespac->queryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id FROM materiels, marques WHERE materiels.marque_id = marques.marque_id AND marque_marque='$marque_marque' order by mat_nom" );

	echo "<p><small>" . count($liste_des_materiels) . " mat�riel(s)</small></p>";
	
	$fp = fopen('../dump/extraction.csv', 'w+');	//Ouverture du fichier
	fputcsv($fp, array('nom', 'dsit', 'serial', 'etat', 'modele', 'type', 'stype'), ',' );	// ENTETES
	echo "<center><a href='./dump/extraction.csv' target=_blank>fichier CSV</a></center><br>";
	
?>


<center>
	
	<table id="myTable" width=620>
		<th>nom</th>
		<th>dsit</th>
		<th>serial</th>
		<th>etat</th>
		<th>mod�le</th>
		<th>type</th>
		<th>s/type</th>
		
		<?PHP	
			
			$compteur = 0;	
			// On parcourt le tableau
			foreach ($liste_des_materiels as $record ) {
				// On �crit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";

				echo "<tr class=$tr_class>";
					$nom 		= $record[0];
					$dsit 		= $record[1];
					$serial 	= $record[2];
					$etat 		= $record[3];
					$marque 	= $record[4];
					$model 		= $record[5];
					$type 		= $record[6];
					$stype 		= $record[7];
					$id 		= $record[8];
	
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
	// On se d�connecte de la db
	$db_gespac->disconnect();

?>

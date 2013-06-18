<?PHP

	$rq_export = $_POST['rqsql'];


	/*	CREATION DU FICHIER D'EXPORT INVENTAIRE	*/

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');

	// cnx � gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);

	// stockage des lignes retourn�es par sql dans un tableau
	$liste_export = $con_gespac->QueryAll ( $rq_export );

	$filename = "export_perso.csv";

	$fp = fopen('../../dump/' .$filename, 'w+');

	foreach ($liste_export as $record) {

		$my_line = array();

		foreach ($record as $field)
			array_push($my_line, $field);
			
		fputcsv($fp, $my_line, ',');
	}

	fclose($fp);

	echo "<center><h1><a href='./dump/$filename'>Fichier CSV Export Perso</a></h1></center>";

?>

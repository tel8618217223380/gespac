<?PHP
session_start();


	/* 
		fichier de creation / modif / suppr du mat�riel
		
		Si j'ai un ID c'est une modification
		Si j'en ai pas c'est une cr�ation
		
		reste � coder pour la suppression
	*/

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
	// cnx � la base de donn�es GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");
	
		
	// on r�cup�re les param�tres de l'url	
	$action 	= $_GET['action'];
	$id 		= $_GET['id'];
	

	/**************** SAUVEGARDE de l'ETAT des Entetes dans les sessions ********************/
	
	if ( $action == 'entetes' ) {	
		$_SESSION['entetes'] = $_GET['value'];
	}
	
	
	
	/*********************************************
	*
	*		ACTIONS SUR MATERIELS
	*
	**********************************************/
	
	
	/**************** CHOIX ADRESSE MAC ********************/
	
	if ( $action == 'mod_mac' ) {	
	
		$id			= $_GET ['mat_id'];
		$mac	 	= $_GET ['mac'];
		
		//R�cup�ration du nom de la machine pour les logs
		$nom_mat = $con_gespac->QueryOne("SELECT mat_nom FROM materiels WHERE mat_id=$id");
		
		$req_modif_mac_materiel = "UPDATE materiels SET mat_mac='$mac' WHERE mat_id=$id";
		$con_gespac->Execute ( $req_modif_mac_materiel );
			
		//On logue la requ�te SQL
		$log->Insert( $req_modif_mac_materiel );
		
		//Insertion d'un log

		$log_texte = "L\'adresse MAC <b>$mac</b> du mat�riel <b>$nom_mat</b> a �t� modifi�e";

		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification mat�riel', '$log_texte' );";
		$db_gespac->Execute ( $req_log_modif_mat );
	}
	
	

	/**************** SUPPRESSION ********************/

	
	if ( $action == 'suppr' ) {	
			
	
			//Insertion d'un log (avant la suppression!)
			//On r�cup�re le nom du mat�riel en fonction du mat_id
			$liste_materiel = $con_gespac->QueryRow ( "SELECT mat_nom, mat_serial FROM materiels WHERE mat_id = $id" );
			$mat_nom 	= $liste_materiel [0];
			$mat_serial = $liste_materiel [1];

			$log_texte = "Le materiel <b>$mat_nom</b> (num�ro de s�rie : <b>$mat_serial</b>) a �t� supprim�.";
				
			$req_log_suppr_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression mat�riel', '$log_texte' );";
			$con_gespac->Execute ( $req_log_suppr_mat );
				
			//Suppression
					
			$req_suppr_materiel = "DELETE FROM materiels WHERE mat_id=$id;";
			$con_gespac->Execute ( $req_suppr_materiel );
			
			// On log la requ�te SQL
			$log->Insert(  $req_suppr_materiel );

	}

		
	/**************** MODIFICATION D'UN LOT ********************/
		
	if ( $action == 'modlot' ) {
		
		$lot		= addslashes(utf8_decode(urldecode($_POST ['lot'])));
		$etat   	= addslashes(utf8_decode(urldecode($_POST ['etat'])));
		$salle  	= addslashes(utf8_decode(urldecode($_POST ['salle'])));
		$type   	= addslashes(utf8_decode(urldecode($_POST ['type'])));
		$modele 	= addslashes(utf8_decode(urldecode($_POST ['modele'])));
		$origine 	= addslashes(utf8_decode(urldecode($_POST ['origine'])));
		
		$message_pret_ok = "";
		$message_pret_ko = "";
	
		//$liste_noms   = "";
		//$liste_serial = "";
		
		$lot_array = explode(";", $lot);
		
		foreach ($lot_array as $item) {
			
			if ( $item <> "" ) {	// permet de virer les �l�ments vides
				
				// test si la machine est pr�t�e ou pas
				@$mat_id = $con_gespac->QueryOne ( "SELECT mat_id FROM materiels WHERE user_id<>1 AND mat_id=$item" );
				
				if ( !isset($mat_id) ) {	// la machine n'est pas pr�t�e ($mat_id n'existe pas)
					if ( $item <> "") {
						// Si l'�tat est modifi� on fait un update sur ce champ
						$sql_etat = $etat == "" ? "" : " mat_etat='$etat' ";
						
						if ( $origine <> "" ) {
							// met on ou non la virgule avant en fonction de l'existence de la variable pr�c�dente (oula, dure � comprendre �a ...)
							$sql_origine = $sql_etat == "" ? " mat_origine='$origine' " : ", mat_origine='$origine' " ;
							
						} else { $sql_origine = ""; }
						
						
						if ( $salle <> "" ) {
							// on r�cup�re le num�ro d'id de salle que l'on veut modifier dans la table materiels avec comme clause WHERE le nom de salle post�
							$salle_id = $con_gespac->QueryOne ( "SELECT salle_id FROM salles WHERE salle_nom='$salle'" );

							// dans la rq sql, met on ou non la virgule avant en fonction de l'existence de la variable pr�c�dente (oula, dure � comprendre �a ...)
							
							if ( $sql_origine == "" && $sql_etat == "" ) $sql_salle = " salle_id=$salle_id ";
							else $sql_salle = ", salle_id=$salle_id " ;

						} else { $sql_salle = ""; }
						
						if ( $type <> "" ) {
							// on r�cup�re le num�ro d'id de marque que l'on veut modifier dans la table materiels avec comme clause WHERE le type, le sous type, la marque et le modele de marque
							$marque_id = $con_gespac->QueryOne ( "SELECT marque_id FROM marques WHERE marque_type='$type' AND marque_stype='$stype' AND marque_marque='$marque' AND marque_model='$modele'" );
							
							if ( $sql_origine == "" && $sql_etat == "" && $sql_salle == "" ) $sql_marque = " mat_salle=$marque_id";
							else $sql_marque = " , marque_id=$marque_id" ;
							
						} else { $sql_marque = ""; }
						
						$req_modif_materiel = "UPDATE materiels SET " . $sql_etat . $sql_origine . $sql_salle . $sql_marque . " WHERE mat_id=$item ;";
						$con_gespac->Execute ( $req_modif_materiel );
						
						$message_pret_ok = "La modification des mat�riels s�lectionn�s a �t� effectu�e.<br>";
						
						//on r�cup�re le nom et le serial de chaque item
						$req_nom_serial_materiel = $con_gespac->QueryRow ("SELECT mat_nom, mat_serial FROM materiels WHERE mat_id=$item");
						$liste_noms_serial   .=  '<b>'.$req_nom_serial_materiel[0].' (</b>serial : <b>'.$req_nom_serial_materiel[1].')</b>, ';
						
						// On log la requ�te SQL
						$log->Insert( $req_modif_materiel );
					}
				} else { // la machine est pr�t�e ; on r�cup�re le nom
					$mat_nom = $con_gespac->QueryOne ( "SELECT mat_nom FROM materiels WHERE mat_id=$item" );
					$message_pret_ko .= "Le mat�riel <b>$mat_nom</b> est pr�t�. Merci de le rendre avant r�affectation !<br>";
				}
				
			//Insertion d'un log
			//on supprime les caract�res en fin de chaine
			$liste_noms_serial = trim ($liste_noms_serial, ", ");
			$log_texte = "Les materiels $liste_noms_serial ont �t� modifi�s.";

			$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification mat�riel', '$log_texte' );";
			$con_gespac->Execute ( $req_log_modif_mat );

		} 
	}
	echo $message_pret_ok.$message_pret_ko;
}
	
	
	/**************** RENOMMAGE D'UN LOT ********************/
		
	if ( $action == 'renomlot' ) {
		
		$lot		= addslashes(utf8_decode(urldecode($_POST ['lot'])));
		$prefixe   	= addslashes(utf8_decode(urldecode($_POST ['prefixe'])));
		$suffixe   	= $_POST ['suffixe'];
		

		$lot_array = explode(";", $lot);
		
		$sequence = $suffixe == "on" ? 1 : "" ;
		
		foreach ($lot_array as $item) {
			
			if ($item <> "") {
				//on r�cup�re le nom initial
				$req_materiel_old = $con_gespac->QueryRow("SELECT mat_nom, mat_serial FROM materiels WHERE mat_id=$item");
				
				$req_renomme_materiel = "UPDATE materiels SET mat_nom='" . $prefixe ."". $sequence . "' WHERE mat_id=$item ;";
				$con_gespac->Execute ( $req_renomme_materiel );
				
				if ( $suffixe == 'on' ) $sequence++;	//Pour faire un suffixe s�quentiel
				$req_materiel_new = $con_gespac->QueryRow("SELECT mat_nom, mat_serial FROM materiels WHERE mat_id=$item");
				
				$liste_nom_materiels .= 'Le nom initial (<b>'.$req_materiel_old[0].'</b>) a �t� chang� en <b>'.$req_materiel_new[0].'</b>. Le num�ro de s�rie de la machine est : <b>'.$req_materiel_new[1].'</b>.<br>';
				
			}
			
			// On log la requ�te SQL
			$log->Insert( $req_renomme_materiel );
		
		}
	
		//Insertion d'un log
		$log_texte = $liste_nom_materiels;

		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification mat�riel', '$log_texte' );";
		$con_gespac->Execute ( $req_log_modif_mat );

	}
	
	/**************** MODIFICATION ********************/
		
	if ( $action == 'mod' ) {
	
		$id			= $_POST ['materiel_id'];
		$marque_id	= $_POST['marque_id'];
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		$dsit 		= addslashes(utf8_decode(urldecode($_POST ['dsit'])));
		$serial		= addslashes(utf8_decode(urldecode($_POST ['serial'])));
		$etat   	= addslashes(utf8_decode(urldecode($_POST ['etat'])));
		$gign   	= addslashes(utf8_decode(urldecode($_POST ['num_gign'])));
		$salle  	= $_POST ['salle'];
		$origine 	= addslashes(utf8_decode(urldecode($_POST ['origine'])));
		$mac_input	= addslashes(utf8_decode(urldecode($_POST ['mac_input'])));
		$mac_radio	= addslashes(utf8_decode(urldecode($_POST ['mac_radio'])));


		// En fonction du champ rempli (input ou radio) on r�cup�re l'une ou l'autre des valeurs
		$mac = $mac_input <> "" ? $mac_radio : $mac_input ;
				
		// on r�cup�re le num�ro d'id de salle que l'on veut modifier dans la table materiels avec comme clause WHERE le nom de salle post�
		$salle_id = $con_gespac->QueryOne ( "SELECT salle_id FROM salles WHERE salle_nom='" . utf8_decode($salle) ."'" );
		
		// Si un dossier est entr�, on concat�ne etat et dossier, sinon on ne colle que l'�tat.
		if ( $gign ) $etat = $etat . "-" . $gign;
			
		// test si la machine est pr�t�e ou pas
		@$mat_id = $con_gespac->QueryOne ( "SELECT mat_id FROM materiels WHERE user_id<>1 AND mat_id=$id" );
		
		if ( !isset($mat_id) ) {// la machine n'est pas pr�t�e ($mat_id n'existe pas)
			if ( $marque_id ) {
				$req_modif_materiel = "UPDATE materiels SET mat_nom='$nom', mat_dsit='$dsit', mat_serial='$serial', mat_etat='$etat', salle_id=$salle_id, marque_id=$marque_id, mat_origine = '$origine', mat_mac='$mac' WHERE mat_id=$id";
				$con_gespac->Execute ( $req_modif_materiel );
				
				echo "<small>Le mat�riel <b>$nom</b> a bien �t� modifi�.</small>";
				
				// On log la requ�te SQL
				$log->Insert( $req_modif_materiel );
			} 
		
			//Insertion d'un log

			$log_texte = "Le mat�riel <b>$nom</b> ayant pour num�ro de s�rie <b>$serial</b> a �t� modifi�";

			$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification mat�riel', '$log_texte' );";
			$con_gespac->Execute ( $req_log_modif_mat );
	
		} else {
			echo "Le mat�riel <b>$nom</b> est pr�t�. Merci de le rendre avant de modifier sa salle.";
		}
	}	
	
	/**************** INSERTION ********************/
	
	if ( $action == 'add' ) {
		$marque_id	= $_POST['marque_id'];
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		$dsit 		= addslashes(utf8_decode(urldecode($_POST ['dsit'])));
		$serial		= addslashes(utf8_decode(urldecode($_POST ['serial'])));
		$etat   	= addslashes(utf8_decode(urldecode($_POST ['etat'])));
		$salle  	= addslashes(utf8_decode(urldecode($_POST ['salle'])));
		$type   	= addslashes(utf8_decode(urldecode($_POST ['type'])));
		$stype   	= addslashes(utf8_decode(urldecode($_POST ['stype'])));
		$marque   	= addslashes(utf8_decode(urldecode($_POST ['marque'])));
		$modele 	= addslashes(utf8_decode(urldecode($_POST ['modele'])));
		$origine 	= addslashes(utf8_decode(urldecode($_POST ['origine'])));
		$mac	 	= addslashes(utf8_decode(urldecode($_POST ['mac'])));
		
		
		// on r�cup�re le num�ro d'id de salle que l'on veut modifier dans la table materiels avec comme clause WHERE le nom de salle post�
		$salle_id = $con_gespac->QueryOne ( "SELECT salle_id FROM salles WHERE salle_nom='$salle'" );
		

		//on fait notre requ�te d'insertion avec le marque_id
		$req_add_materiel = "INSERT INTO materiels ( mat_nom, mat_dsit, mat_serial, mat_etat, salle_id, marque_id, mat_origine, mat_mac) VALUES ( '$nom', '$dsit', '$serial', '$etat', '$salle_id', $marque_id, '$origine', '$mac')";
		$con_gespac->Execute ( $req_add_materiel );
		
		// On log la requ�te SQL
		$log->Insert( $req_add_materiel );
		
		echo "<small>Ajout du mat�riel <b>$nom</b> !</small>";
		
		//Insertion d'un log

		$log_texte = "Le mat�riel <b>$nom</b> ayant pour num�ro de s�rie <b>$serial</b> a �t� cr��.";
		
		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation mat�riel', '$log_texte');";
		$con_gespac->Execute ( $req_log_modif_mat );
	}
	
	/********** INSERTION D'UN MATERIEL PAR UNE MARQUE ***********/
	
	if ( $action == 'add_mat_marque' ) {
		
		$marque_id  = $_POST['add_marque_materiel'];
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		$dsit 		= addslashes(utf8_decode(urldecode($_POST ['dsit'])));
		$serial		= addslashes(utf8_decode(urldecode($_POST ['serial'])));
		$etat   	= addslashes(utf8_decode(urldecode($_POST ['etat'])));
		$salle  	= addslashes(utf8_decode(urldecode($_POST ['salle'])));
		$origine 	= addslashes(utf8_decode(urldecode($_POST ['origine'])));
		$mac	 	= addslashes(utf8_decode(urldecode($_POST ['mac'])));
		
		// on r�cup�re le num�ro d'id de salle que l'on veut modifier dans la table materiels avec comme clause WHERE le nom de salle post�
		$salle_id = $con_gespac->QueryOne ( "SELECT salle_id FROM salles WHERE salle_nom='$salle'" );
		
		
		$req_add_marque_materiel = "INSERT INTO materiels ( mat_nom, mat_dsit, mat_serial, mat_etat, salle_id, marque_id, mat_origine, mat_mac) VALUES ( '$nom', '$dsit', '$serial', '$etat', '$salle_id', $marque_id, '$origine', '$mac')";
		$con_gespac->Execute ( $req_add_marque_materiel );
		
		// On log la requ�te SQL
		$log->Insert( $req_add_marque_materiel );
		
		echo "<small>Ajout du mat�riel <b>$nom</b> !</small>";
		
		//Insertion d'un log

		$log_texte = "Le mat�riel <b>$nom</b> ayant pour num�ro de s�rie <b>$serial</b> a �t� cr��.";
		
		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation mat�riel', '$log_texte');";
		$con_gespac->Execute ( $req_log_modif_mat );
	}
	

	/********** AFFECTATION DE SALLE ***********/
	
	if ( $action == 'affect' ) {
	
		$mat_ids 	= addslashes(utf8_decode(urldecode($_POST['materiel_a_poster'])));
		$salle_id 	= addslashes(utf8_decode(urldecode($_POST['salle_select'])));
		
		$mat_ids_array = explode (";", $mat_ids);
		$mat_ids_unique = array_unique ($mat_ids_array);
		
		$message_pret_ok = "";
		$message_pret_ko = "";
		$mat_nom = "";
		
		foreach ($mat_ids_unique as $id) {
			
			if ($id <> "") {	//On ne g�re que les $id non nuls -> Pas tr�s beau : le pb vient du premier ; dans la chaine id
			
				// test si la machine est pr�t�e ou pas
				@$mat_id = $con_gespac->QueryOne ( "SELECT mat_id FROM materiels WHERE user_id<>1 AND mat_id=$id" );
				//$mat_id = @$rq_machine_pretee;	// crado : le @ permet de ne pas afficher d'erreur si la requete ne renvoie rien. A modifier, �videment
				
				if ( !isset($mat_id) ) {	// la machine n'est pas pr�t�e ($mat_id n'existe pas)
					if ( $id <> "") {
						
						$req_modif_apreter = "UPDATE materiels SET salle_id = $salle_id WHERE mat_id = $id";
						$con_gespac->Execute ( $req_modif_apreter );
						
						// On r�cup�re le nom de la salle en fonction du $salle_id et le nom de chaque machine (message + logs)
						$salle_nom 	  = $con_gespac->QueryOne ( "SELECT salle_nom FROM salles WHERE salle_id = $salle_id" );
						
						$message_pret_ok = "R�affectation des mat�riels s�lectionn�s dans la salle <b>$salle_nom</b>.<br>";
						
						// On log la requ�te SQL
						$log->Insert( $req_modif_apreter );
					
					}
					//Insertion d'un log
					// On r�cup�re le nom de la salle en fonction du $salle_id et le nom de chaque machine (message + logs)
					$nom_materiel = $con_gespac->QueryOne ( "SELECT mat_nom FROM materiels WHERE mat_id = $id" );
					$salle_nom 	  = $con_gespac->QueryOne ( "SELECT salle_nom FROM salles WHERE salle_id = $salle_id" );
					
					$log_texte .= "R�affectation de <b>$nom_materiel</b> dans la salle <b>$salle_nom</b><br> ";
					
					
				} else {	// la machine est pr�t�e ($mat_id existe)
					$mat_nom = $con_gespac->QueryOne ( "SELECT mat_nom FROM materiels WHERE mat_id=$id" );
					$message_pret_ko .= "Le mat�riel <b>$mat_nom</b> est pr�t�. Merci de le rendre avant r�affectation !<br>";
					
				}
			}
		}
		
		echo $message_pret_ok.$message_pret_ko;
		
		$req_log_affect_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Affectation salle', '$log_texte' );";
		$con_gespac->Execute ( $req_log_affect_salle );
	}

?>



	
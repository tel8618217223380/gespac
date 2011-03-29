<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
		<!--	CHROME FRAME	-->
		<meta http-equiv="X-UA-Compatible" content="chrome=1" />
		
		<!--	CODAGE	-->
		<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 
		
		<!--	FAVICON	-->
		<link rel="SHORTCUT ICON" href="./gespac/img/favicon.ico"/>
		
		<!--	JS	-->
		<script type="text/javascript" src="./gespac/js/mootools-1.2.3-core-yc.js"></script>	
		<script type="text/javascript" src="./gespac/js/mootools-1.2.3.1-more.js"></script>
		<script type="text/javascript" src="./gespac/js/main.js"></script>
		
		<!--	CSS	-->
		<link rel="stylesheet" href="./gespac/css/style_ff.css" type="text/css" media="screen" />
		
		<!--Script de d�tection pour savoir si il y a un popup killer-->
		<script type="text/JavaScript" language="javascript">
�			var mine = window.open('','','width=1,height=1,left=0,top=0,scrollbars=no');
			if(mine)
				var popUpsBlocked = false
			else
				var popUpsBlocked = true
			mine.close()
		</script>	

</head>

<body>

	<script type="text/javascript" 
		src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"> </script>
	 
	<div id="placeholder"></div>

	<script>
	 CFInstall.check({
		mode: "inline",
		node: "placeholder",
		destination: "cf:http://localhost/GESPAC3/"
	  });
	</script>
	
	<div id=portail-menu>
	
	<h3>
	
	
	<?php

	//installation de la base GESPAC
	
	require_once ('./include/config.php');

	//Analyse des versions OCS et FOG
	$file_ocs = '/usr/share/ocsinventory-reports/ocsreports/preferences.php';//fichier ou est stock� la version du GUI d'OCS
	if (file_exists($file_ocs)) {
		require_once ('/usr/share/ocsinventory-reports/ocsreports/preferences.php');
		$version_ocs = GUI_VER;//r�cup�re la version du GUI d'OCS
	}
	else {$version_ocs = "Il semblerait qu'OCS ne soit pas install�";}//le fichier n'est pas trouv�
	
	$file_fog = '/var/www/fog/commons/config.php';//fichier ou est stock� la version de FOG
	if (file_exists($file_fog)) {
		require_once ('/var/www/fog/commons/config.php'); 
		$version_fog = FOG_VERSION;//r�cup�re la version de FOG
	}
	else {$version_fog = "Il semblerait que FOG ne soit pas install�";}//Fichier config FOG non trouv�
	
		//Les commandes sous linux
		if (!EXEC('uname -r')) {//on v�rifie le syst�me avec une commande sh
			$version_gespac = 'Non d�termin� car le syst�me ne semble pas �tre un Linux';
			$version_sqlgespac = 'Non d�termin� car le syst�me ne semble pas �tre un Linux';
			$version_linux = 'Horreur votre syst�me est un WIN32';//Message un peu dur, mais salutaire!!!
		}
		else {
			$version_gespac = EXEC('apt-show-versions gespac');
			$version_sqlgespac = EXEC('apt-show-versions sql-gespac');
			$version_linux = EXEC('uname -r');
		}
	
		// on v�rifie la connectivit� avec le serveur avant d'aller plus loin
		if(!mysql_connect($hostname_gespac, $username_gespac, $password_gespac)) {
			echo 'Merci de renseigner le fichier "config.php" se trouvant dans le dossier include.<br>';
			exit();
		}

		// on v�rifie la connectivit� avec la base avant d'aller plus loin	
		if(!mysql_select_db($database_gespac)) {
			echo '<img src="./gespac/img/info.png"><br>
			vous devez installer au pr�alable la base de donn�es en cliquant <a href="install_mysql/installation.php">ici</a>';
			exit();
		}	

		
		
		session_start();
		
		// on v�rifie si l'utilisateur est identifi�
		if (!isset( $_SESSION['login'])) {
			// la variable de session n'existe pas, donc l'utilisateur n'est pas authentifi� -> On redirige sur la page permettant de s'authentifier
			echo '<img src="./gespac/img/gespac.png" height=48> version d�veloppement';
			include 'login.php';
			exit();	// on arr�te l'ex�cution

		} else {

			//$display_icon = ( $_SESSION['grade'] < 2 ) ? "" : "none" ;

			// si le grade du compte est root, on donne automatiquement les droits d'acc�s aux icones. Sinon, on teste si le compte a acc�s aux icones sinon.
			
				
			echo "<div id=portail-menu-item><a href='./gespac'> 
				<img src='./gespac/img/gespac.png' height=48><br>GESPAC </a></div>";
			
			include ('gespac/config/databases.php');
			include ('gespac/config/pear.php');
				
			// adresse de connexion � la base de donn�es
			$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

			// cnx � la base de donn�es GESPAC
			$db_gespac 	= & MDB2::factory($dsn_gespac);

			// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
			$liste_des_icones = $db_gespac->queryAll ( "SELECT mp_id, mp_nom, mp_url, mp_icone FROM menu_portail ORDER BY mp_nom" );	
			
				
			foreach ( $liste_des_icones as $record ) {
			
				$mp_id 		= $record[0];
				$mp_nom 	= $record[1];
				$mp_url 	= $record[2];
				$mp_icone 	= $record[3];
				
				$affiche_item = ($_SESSION['grade'] == 'root') ? true : preg_match ("#item$mp_id#", $_SESSION['menu_portail']);
				
				if ( $affiche_item )
					echo "<div id=portail-menu-item><a href='$mp_url' target=_blank> <img src='./gespac/img/$mp_icone' height=48><br>$mp_nom</a> </div>";

			}	
			
			echo "<div style='float:right;' id=portail-menu-item><a href='./gespac/gestion_authentification/logout.php'> 
				<img src='./gespac/img/cancel.png' height=48><br>D�connexion </a></div>";

			echo "<div class='spacer'> </div>";
	
			
		}
	?>
	
	<!--On lance la d�tection du popup killer -->
	<script type="text/JavaScript" language="JavaScript">
		if ( popUpsBlocked ) alert('POPUP KILLERS :\nPr�ter ou rendre un portable g�n�re un popup pour les conventions.\nLe popup killer bloquera l`affichage de ces conventions.\nPensez � autoriser les popups pour GESPAC.');
	</script>
	
	</h3>
	</div>
	
	<br/>
	
	<div id=portail-conteneur>
		<b>GESPAC : </b><?php echo $version_gespac;?><br/>
		<b>SQL-GESPAC : </b><?php echo $version_sqlgespac;?><br/>
		<b>Linux kernel : </b><?php echo $version_linux;?><br/>
		<b><?php echo $_SERVER['SERVER_SIGNATURE'];?></b>
		<b>PHP : </b><?php echo  phpversion();?><b/>r>
		<b>Zend engine version :</b> <?php echo zend_version(); ?><br/>
		<b>Version GUI OCS : </b><?php echo $version_ocs;?><br/>
		<b>Version FOG :</b> <?php echo $version_fog;?><br/>
		<b>Navigateur utilis� : </b><?php echo $_SERVER["HTTP_USER_AGENT"];?><br/><br/><br/>
				<b>SITE OFFICIEL : </b><br/>
					<a href="http://gespac13.free.fr" target=_blank>GESPAC13</a> (Les proc�dures et manuels valid�s)<br/><br/>
		<b>NAVIGATEURS : </b><br/>
			- Gespac marche mieux avec Firefox 3.5.x, Firefox 3.6, Chrome et Safari (globalement si le navigateur g�re le css3, pas de probl�me)<br/>
			- Il marche avec pratiquement tous les autres navigateurs, mais c'est moins joli (par exemple Opera 10.5 ne g�re pas les fonctions css3 utilis�es, donc c'est carr�) <br/>
			- Il ne marche pas avec Internet Explorer mais si vous voulez quand m�me l'utiliser, Gespac tentera d'installer Google Frame (vous aurez donc l'interface ie avec le moteur webkit de chrome).<br/>
	</div>


</body>
</html>

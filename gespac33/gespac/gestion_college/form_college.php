<?PHP	#********************************************	# cr�ation et postage des donn�es du coll�ge	#	# cr�ation de la salle D3E et STOCK	#	#********************************************		include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...?><!--  SERVEUR AJAX <script type="text/javascript" src="server.php?client=all"></script>--><script>	window.addEvent('domready', function(){		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire			new Event(e).stop();			new Request({				method: this.method,				url: this.action,				onSuccess: function(responseText, responseXML) {					$('target').set('html', responseText);					window.setTimeout("$('conteneur').load('gestion_college/voir_college.php');", 1500);				}			}).send(this.toQueryString());		});		});// ferme la smoothbox et rafraichis la page	function refresh_quit () {		// lance la fonction avec un d�lais de 1000ms		window.setTimeout("HTML_AJAX.replace('conteneur', 'gestion_college/voir_college.php');", 1000);		TB_remove();	}// Validation du formulairefunction validation () {	var bt_submit = document.getElementById("post_college");	var clg_uai = document.getElementById("clg_uai").value;	var clg_nom = document.getElementById("clg_nom").value;	var clg_ville = document.getElementById("clg_ville").value;	if (clg_uai == "" || clg_nom == "" || clg_ville == "") {		bt_submit.disabled = true;	} else {		bt_submit.disabled = false;	}}</script><?PHP		$clg_uai = $_GET['id'];		if ( $clg_uai ) {			#***************************************************************************#		# 		Requete pour r�cup�rer les donn�es des champs du coll�ge			#		#***************************************************************************#				// adresse de connexion � la base de donn�es			$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';					// cnx � la base de donn�es GESPAC		$db_gespac 	= & MDB2::factory($dsn_gespac);		// stockage des lignes retourn�es par sql dans un tableau nomm� avec originalit� "array" (mais "tableau" peut aussi marcher)		$college_a_modifier = $db_gespac->queryAll ( "SELECT * FROM college WHERE clg_uai='$clg_uai'" );				$clg_uai 		= $college_a_modifier[0][0];		$clg_nom 		= $college_a_modifier[0][1];		$clg_ati 		= $college_a_modifier[0][2];		$clg_ati_mail 	= $college_a_modifier[0][3];		$clg_adresse 	= $college_a_modifier[0][4];		$clg_cp 		= $college_a_modifier[0][5];		$clg_ville 		= $college_a_modifier[0][6];		$clg_tel 		= $college_a_modifier[0][7];		$clg_fax 		= $college_a_modifier[0][8];		$clg_web 		= $college_a_modifier[0][9];		$clg_grr 		= $college_a_modifier[0][10];?>			<h3>Modifier les informations de votre coll�ge</h3><hr><br><br><script>	// Donne le focus au premier champ du formulaire	$('clg_uai').focus();</script><form name="post_form" id="post_form" action="gestion_college/post_college.php?action=mod" method="post">			<center>			<input type=hidden name=old_uai value=<?PHP echo $clg_uai ?> />						<table width=500>				<tr>					<TD>UAI *</TD>					<TD align=left><input type=text name=clg_uai id=clg_uai size=8 maxlength=8 value= "<?PHP echo $clg_uai; ?>" onkeyup="validation();" /></TD>				</tr>								<tr>					<TD>Nom coll�ge *</TD>					<TD align=left><input type=text name=clg_nom id=clg_nom size=30 value= "<?PHP echo $clg_nom; ?>" onkeyup="validation();" /></TD>				</tr>							<tr>					<TD>Nom et pr�nom ATI</TD>					<TD align=left><input type=text name=clg_ati size=50 value= "<?PHP echo $clg_ati; ?>"/></TD>				</tr>											<tr>					<TD>Mail ATI</TD>					<TD align=left><input type=text size=50 name=clg_ati_mail value= "<?PHP echo $clg_ati_mail; ?>"/></TD>				</tr>							<tr>					<TD>Adresse</TD>					<TD align=left><input type=text name=clg_adresse size=50 value= "<?PHP echo $clg_adresse; ?>"/></TD>				</tr>							<tr>					<TD>Code Postal</TD>					<TD align=left><input type=text name=clg_cp size=5 maxlength=5 value= "<?PHP echo $clg_cp; ?>"/></TD>				</tr>							<tr>					<TD>Ville *</TD>					<TD align=left><input type=text name=clg_ville id=clg_ville value= "<?PHP echo $clg_ville; ?>" onkeyup="validation();"/></TD>				</tr>								<tr>					<TD>Tel</TD>					<TD align=left><input type=text name=clg_tel size=10 maxlength=10 value= "<?PHP echo $clg_tel; ?>"/></TD>				</tr>							<tr>					<TD>Fax</TD>					<TD align=left><input type=text name=clg_fax size=10 maxlength=10 value= "<?PHP echo $clg_fax; ?>"/></TD>				</tr>							<tr>					<TD>Site web du coll�ge</TD>					<TD align=left>http://<input type=text name=clg_web size=50 value= "<?PHP echo $clg_web; ?>"/></TD>				</tr>											<tr>					<TD>Acc�s GRR</TD>					<TD align=left>http://<input type=text name=clg_grr size=50 value= "<?PHP echo $clg_grr; ?>"/></TD>				</tr>								</table>			<br><input type=submit name=Envoyer value="Modifier les informations" id="post_college" /></form></center><?PHP	} else {?><center><h3>Cr�ation des informations de votre coll�ge</h3>	<hr>	<script>		// A la cr�ation, on masque tous les boutons afin d'obliger l'utilisateur � saisir son coll�ge		$('main_menu').style.display = 'none';				// Donne le focus au premier champ du formulaire		$('clg_uai').focus();	</script><form onsubmit="return !HTML_AJAX.formSubmit(this,'target');" action="gestion_college/post_college.php?action=creat" method="post" name="frmTest" id="frmTest">			<center>			<table width=500>				<tr>					<TD>UAI *</TD>					<TD align=left><input type=text id=clg_uai name=clg_uai size=8 maxlength=8 onkeyup="validation();"/></TD>				</tr>							<tr>					<TD>Nom coll�ge *</TD>					<TD align=left><input type=text id=clg_nom name=clg_nom size=30  onkeyup="validation();"/></TD>				</tr>							<tr>					<TD>Nom et pr�nom ATI</TD>					<TD align=left><input type=text id=clg_ati name=clg_ati /></TD>				</tr>											<tr>					<TD>Mail ATI</TD>					<TD align=left><input type=text id=clg_ati_mail name=clg_ati_mail /></TD>				</tr>							<tr>					<TD>Adresse</TD>					<TD align=left><input type=text id=clg_adresse name=clg_adresse maxlength=40 /></TD>				</tr>							<tr>					<TD>Code Postal</TD>					<TD align=left><input type=text name=clg_cp size=5 maxlength=5 /></TD>				</tr>							<tr>					<TD>Ville *</TD>					<TD align=left><input type=text name=clg_ville id=clg_ville onkeyup="validation();" /></TD>				</tr>								<tr>					<TD>Tel</TD>					<TD align=left><input type=text name=clg_tel /></TD>				</tr>							<tr>					<TD>Fax</TD>					<TD align=left><input type=text name=clg_fax /></TD>				</tr>							<tr>					<TD>Site web du coll�ge</TD>					<TD align=left>http://<input type=text name=clg_web /></TD>				</tr>											<tr>					<TD>Acc�s GRR</TD>					<TD align=left>http://<input type=text size=50 name=clg_grr /></TD>				</tr>								</table>			<br>	<input type=submit name=Envoyer value="Cr�er le coll�ge" id="post_college" onClick="refresh_quit();" disabled/></form></center> <?PHP	} ?><!--	DIV target pour Ajax	--><div id="target"></div>
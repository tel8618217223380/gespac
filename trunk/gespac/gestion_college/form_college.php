<?PHP	#********************************************	# création et postage des données du collège	#	# création de la salle D3E et STOCK	#	#********************************************			// lib	include ('../config/databases.php');	// fichiers de configuration des bases de données	require_once ('../fonctions.php');	include_once ('../../class/Sql.class.php');	?> <script>	window.addEvent('domready', function(){		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire			new Event(e).stop();			new Request({				method: this.method,				url: this.action,				onSuccess: function(responseText, responseXML) {					$('target').setStyle("display","block");					$('target').set('html', responseText);					SexyLightbox.close();					window.setTimeout("document.location.href='index.php?page=college'", 2500);				}			}).send(this.toQueryString());		});		});		// Validation du formulaire	function validation () {		var bt_submit = $("post_college");		var clg_uai   = $("clg_uai").value;		var clg_nom   = $("clg_nom").value;		var clg_mail  = $("clg_ati_mail").value;		var clg_ville = $("clg_ville").value;		if (clg_uai == "" || clg_nom == "" || clg_mail == "" || clg_ville == "") {			bt_submit.disabled = true;		} else {			bt_submit.disabled = false;		}}</script><?PHP		$clg_uai = $_GET['id'];		if ( $clg_uai ) {			#***************************************************************************#		# 		Requete pour récupérer les données des champs du collège			#		#***************************************************************************#				// cnx à la base de données GESPAC		$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );		// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)		$college_a_modifier = $con_gespac->QueryRow ( "SELECT * FROM college WHERE clg_uai='$clg_uai'" );				$clg_uai 		= $college_a_modifier[0];		$clg_nom 		= $college_a_modifier[1];		$clg_ati 		= $college_a_modifier[2];		$clg_ati_mail 	= $college_a_modifier[3];		$clg_adresse 	= $college_a_modifier[4];		$clg_cp 		= $college_a_modifier[5];		$clg_ville 		= $college_a_modifier[6];		$clg_tel 		= $college_a_modifier[7];		$clg_fax 		= $college_a_modifier[8];		$clg_web 		= $college_a_modifier[9];		$clg_grr 		= $college_a_modifier[10];?><script>	// Donne le focus au premier champ du formulaire	$('clg_uai').focus();</script><form name="post_form" id="post_form" action="gestion_college/post_college.php?action=mod" method="post">			<center>			<input type=hidden name=old_uai value=<?PHP echo $clg_uai ?> />						<table width=500>				<tr>					<TD>UAI *</TD>					<TD align=left><input type=text name=clg_uai id=clg_uai size=8 maxlength=8 value= "<?PHP echo $clg_uai; ?>" onkeyup="validation();" /></TD>				</tr>								<tr>					<TD>Nom collège *</TD>					<TD align=left><input type=text name=clg_nom id=clg_nom size=30 value= "<?PHP echo $clg_nom; ?>" onkeyup="validation();" /></TD>				</tr>							<tr>					<TD>Nom et prénom ATI</TD>					<TD align=left><input type=text name=clg_ati size=50 value= "<?PHP echo $clg_ati; ?>"/></TD>				</tr>											<tr>					<TD>Mail ATI *</TD>					<TD align=left><input type=text size=50 name=clg_ati_mail id=clg_ati_mail value="<?PHP echo $clg_ati_mail; ?>" onkeyup="validation();"/></TD>				</tr>							<tr>					<TD>Adresse</TD>					<TD align=left><input type=text name=clg_adresse size=50 value= "<?PHP echo $clg_adresse; ?>"/></TD>				</tr>							<tr>					<TD>Code Postal</TD>					<TD align=left><input type=text name=clg_cp size=5 maxlength=5 value= "<?PHP echo $clg_cp; ?>"/></TD>				</tr>							<tr>					<TD>Ville *</TD>					<TD align=left><input type=text name=clg_ville id=clg_ville required value= "<?PHP echo $clg_ville; ?>" onkeyup="validation();"/></TD>				</tr>								<tr>					<TD>Tel</TD>					<TD align=left><input type=text name=clg_tel size=10 maxlength=10 value= "<?PHP echo $clg_tel; ?>"/></TD>				</tr>							<tr>					<TD>Fax</TD>					<TD align=left><input type=text name=clg_fax size=10 maxlength=10 value= "<?PHP echo $clg_fax; ?>"/></TD>				</tr>							<tr>					<TD>Site web</TD>					<TD align=left>http://<input type=text name=clg_web size=40 value= "<?PHP echo $clg_web; ?>"/></TD>				</tr>											<tr>					<TD>Accès GRR</TD>					<TD align=left>http://<input type=text name=clg_grr size=40 value= "<?PHP echo $clg_grr; ?>"/></TD>				</tr>								</table>			<br><input type=submit name=Envoyer value="Modifier les informations" id="post_college"/><br><br>	</form>	<a href="#" onclick="alert('Ne pas utiliser le mail CG13 dans le champ Mail ATI. \nCette adresse va permettre un envoi des mails aux différents utilisateurs. ');">INFORMATIONS</a></center><?PHP	} else {?><center>	<script>		// A la création, on masque tous les boutons afin d'obliger l'utilisateur à saisir son collège		$('main_menu').style.display = 'none';				// Donne le focus au premier champ du formulaire		$('clg_uai').focus();	</script>	<form action="gestion_college/post_college.php?action=creat" method="post" name="post_form" id="post_form">				<center>				<table width=500>					<tr>						<TD>UAI *</TD>						<TD align=left><input type=text id=clg_uai name=clg_uai size=8 maxlength=8 onkeyup="validation();"/></TD>					</tr>									<tr>						<TD>Nom collège *</TD>						<TD align=left><input type=text id=clg_nom name=clg_nom size=30  onkeyup="validation();"/></TD>					</tr>									<tr>						<TD>Nom et prénom ATI</TD>						<TD align=left><input type=text id=clg_ati name=clg_ati /></TD>					</tr>													<tr>						<TD>Mail ATI *</TD>						<TD align=left><input type=text id=clg_ati_mail name=clg_ati_mail onkeyup="validation();"/></TD>					</tr>									<tr>						<TD>Adresse</TD>						<TD align=left><input type=text id=clg_adresse name=clg_adresse maxlength=40 /></TD>					</tr>									<tr>						<TD>Code Postal</TD>						<TD align=left><input type=text name=clg_cp size=5 maxlength=5 /></TD>					</tr>									<tr>						<TD>Ville *</TD>						<TD align=left><input type=text name=clg_ville id=clg_ville onkeyup="validation();" /></TD>					</tr>										<tr>						<TD>Tel</TD>						<TD align=left><input type=text name=clg_tel /></TD>					</tr>									<tr>						<TD>Fax</TD>						<TD align=left><input type=text name=clg_fax /></TD>					</tr>									<tr>						<TD>Site web</TD>						<TD align=left>http://<input type=text size=40 name=clg_web /></TD>					</tr>													<tr>						<TD>Accès GRR</TD>						<TD align=left>http://<input type=text size=40 name=clg_grr /></TD>					</tr>										</table>					<br>		<center>			<input type=submit name=Envoyer value="Créer le collège" id="post_college" disabled onclick="$('main_menu').style.display = '';"/><br><br>					</center>	</form>	<a href="#" onclick="alert('Ne pas utiliser le mail CG13 dans le champ Mail ATI. \nCette adresse va permettre un envoi des mails aux différents utilisateurs. ');">INFORMATIONS</a></center> <?PHP	} ?>
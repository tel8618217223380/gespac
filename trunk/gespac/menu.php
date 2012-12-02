<?PHP 


/*

	menu en haut de l'écran
	La gestion des droits est activée de cette façon :
	On a stocké dans un variable de session la liste des pages autorisées en lecture et écriture
	chaque page est identifiée par un code par exemple 02-01 ou 03-02.
	Les codes sont gérés par le fichier menu.txt.
	les fonctions checkdroit et checkalldroits permettent respectivement de tester si un item ou une
	liste d'items est en lecture ou pas.
	On dessine ou pas l'item en fonction de la valeur retournée

*/

?>

	<script type="text/javascript">
		
		// Activation des icones sur clic
		function change_icon_onclick (div) {	
			// on désactive tous les boutons
			
			if ($('accueil')) $('accueil').className = "accueil";
			if ($('inventaire')) $('inventaire').className = "inventaire";
			if ($('demandes')) $('demandes').className = "demandes";
			if ($('donnees')) $('donnees').className = "donnees";
			if ($('prets')) $('prets').className = "prets";
			if ($('utilisateurs')) $('utilisateurs').className = "utilisateurs";
			if ($('plugins')) $('plugins').className = "plugins";
			if ($('info')) $('info').className = "info";
			
			// On active le bon bouton
			$(div).className = div + "-clicked";
		}
				
	</script>


	
	<div id="menu-global">
		
		<div class="menu-block" id="menu-accueil">
			<div class="menu-titre"><img src="img/accueil.png">ACCUEIL</div>
			<div class="menu-items">
				<div class="menu-item">retour au portail</div>
			</div>
		</div>
		
		<div class="menu-block" id="menu-inventaire">
			<div class="menu-titre">INVENTAIRE</div>
			<div class="menu-items">
				<a href="index.php?page=materiels"><div class="menu-item">matériels</div></a>
				<div class="menu-item">marques</div>
				<a href="index.php?page=salles"><div class="menu-item">salles</div></a>
			</div>
		</div>
	
	
	</div>

	

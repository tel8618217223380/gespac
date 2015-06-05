Page récapitulative des changements dans les fonctionnalités de l'application

# Change Log PHP #

**gespac 3.4**
  * Nouveau système pour les dossiers.
  * Gestionnaire de fichiers.
  * Correction d'une faille d'injection SQL.
  * L'import IACA fonctionne à nouveau.
  * Réaffectation d'un matériel à une autre marque.
  * Réparation du module "Récap Fog".
  * Création d'un module de migration des noms d'hôtes Fog avec le num DSIT.
  * correction de bugs divers et variés (clignotement de la page d'accueil, trim des données sur import CSV, cohérence des pages de démarrage ...)
  * hauteur automatique du site en fonction de la résolution d'écran
  * mise à jour de l'adresse mail du compte ATI avec l'adresse dans la fiche collège.
  * Migration vers les classes SQL (pas fini, mais ça avance).


**gespac 3.3.1**
  * possibilité de déverrouiller le champ "serial" dans la modification d'un matériel
  * création/modification/suppression d'un élément du menu portail
  * amélioration du filtre des matériels avec l'opérateur +.
  * bouton CSV dans l'inventaire pour générer un fichier du matériel filtré

**gespac 3.3**
  * refonte complète du moteur ajax. On passe de pear à js/mootools.
  * ajout de la gestion des droits
  * ajout de la notion de grades
  * modification de son propre compte utilisateur (sauf pour le compte root ati)
  * renommer par lot les matériels
  * ajout du mailing pour les dossiers / interventions
  * les logs sont maintenant plus précis
  * possibilité de modifier par lot des utilisateurs
  * ajout d'un code couleur pour les dossiers et les interventions
  * un personnel peut lister uniquement ses dossiers ou tous ceux créés. Pour plus de confidentialité, le nom du créateur du dossier n'apparaît pas dans le listing des demandes (sauf si l'utilisateur a un grade ati).
  * importation d'un fichier de mise à jour des tags DSIT

**gespac 3.2.1**
  * possibilité d'ajouter un matériel qui n'est pas dans la table des correspondances

**gespac 3.2**
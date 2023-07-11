# teamplanning
Remise en roadmap d'un projet créé en 2015. Il s'agit d'une appli php de gestion des ressources utilisable par des teams ou des RH. Utilise des composants relativement anciens (jquery UI 1.9.2).
L'objectif est de maintenir cette application tout en gardant une configuration très légère sans utilisation de bundle ou de frameworks.


Cette application permettra à l'ensemble de vos employés de notifier les jours de congé.
Le calendrier tient compte des jours fériés.

Initialisation
a) Mode fichiers
1) Veuillez télecharger le code source et le dézipper dans un répertoire partagé par votre serveur web (nginx ou apache) ainsi que par une base de données mySql.
2) L'ensemble doit être situé das un dossier nommé "teamplanning" et accessibl par votre serveur web.
3) Executez le fichier team_planning_db situé dans le répertoire sql : cela créera votre base de données "teamplanning", vos tables et certaines données (jours feriés).

b) Déployement docker (OS linux avec docker installé)
Veuillez télecharger le code source et le dézipper dans le répertoire "web" de votre distribution Linux.
Executez le fichier docker-compose.yml; cela aura pour effet de :
- Télecharger les images docker nginx (serveur web), maria-db (base de données) et php fpm 7.3 (interpréteur Php)
- Créer les containers docker à partir des images télechargées
- Déployer votre base de données

Paramétrage
Type de jours de congé
Certains types de jours de congé sont déjà pré-paramétrés (congés payés, RTT, maladie, télétravail). Vous pouvez facilement créer d'autres type jours de congé


Site, département,  service
Teamplanning tente d'être au plus proche de l'organisation d'entreprise. Ainsi vos employés peuvent être affectés à un ou différents sites (localisation géographique comme par exemple Paris, Lyon).
Chaque site peut être subdivisé en différents départements (par exemple achats, comptabilité, infogérance, développement logiciel, RH) et peut être subdivisé en plusieurs services 
(pour le département RH on pourrait avoir les services développement paie, recrutement...).
L'application vous permettra ensuite de filtrer l'affichage par site, département et service.

Si votre organisation a la même structure de départements / services pour l'ensemble des sites, veuillez employer rigoureusement les mêmes libellés :
pourrez ainsi par exemple afficher l'ensemble des ressources affectés au service paie, quelque soit le site. 


Utilisation
Teamplanning a été conçu pour une utilisation rapide et intuitive.
Pour déclarer un jour de congé, cliquez sur la bonne ligne de ressource à la bonne date en colonne.
Sur le bord inférieur de l'écran, L'application vous demandera :
- La date de fin (si vous voulez notifier un congé sur plusieurs jours (par exemple vacances)
- Quel type de jour de congé vous voulez déclarer (proposés dan une liste déroulante).

Après avoir validé ces informations, l'application vous demandera alors confirmation.
Si l'application détecte un chevauchement de dates (jours déjà déclarés dans votre plage de date), vous en serez averti.
Elle vous demandera alors si vous voulez écraser les dates saisies précédemment ou non.

Projection future
- Mécanisme d'authentification
- Création des rôles et des autorisations (policies)
- Création du cycle e vie d'un congé (statut : en demande, confirmé..)

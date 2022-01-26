# Intégration continue - Front-End
Développement d’une application suivant un processus d’intégration continue.  
[Accès à l'API](https://ic.minarox.fr/api/)  
[Documentation](https://github.com/Inge-Info/IC_API/blob/main/API_Doc.yaml) (à ouvrir dans [Swagger](https://editor.swagger.io/))

## Administrateur
Le compte administrateur crée la config des groupes, c'est-à-dire qu'il définit :
* Le nombre d'utilisateurs
* Le nombre de personnes par groupe
* La configuration du dernier groupe : **LAST_MIN** vs **LAST_MAX** :
    * Si le nombre d'utilisateurs est un multiple du nombre de groupes (ex : 20 utilisateurs et 5
      groupes → 5 groupes de 4), ce paramètre n'a pas d'incidence.
    * Si la configuration vaut **LAST_MIN**, le dernier groupe a moins d'utilisateurs que les autres (ex :
      19 utilisateurs et 5 groupes → 4 groupes de 4 et 1 groupe de 3)
    * Si la configuration vaut **LAST_MAX**, le dernier groupe a plus d'utilisateurs que les autres (ex : 19
      utilisateurs et 5 groupes → 5 groupes de 3 et 1 groupe de 4)

## Utilisateurs
Les utilisateurs doivent pouvoir réaliser les actions suivantes :
* Lister les utilisateurs sans groupe
* Demander à créer un nouveau groupe → Création d'un lien d'invitation à partager. Un groupe ne sera
  réellement créé que lorsque au moins une personne accepte l'invitation (jamais de groupe avec un seul
  utilisateur).
* Rejoindre un groupe spécifique déjà créé, mais pas encore complet en utilisant le lien d'invitation
* Demander à être placé automatiquement dans un groupe aléatoire
* Membre d'un groupe : afficher son groupe et les membres du groupe, se retirer du groupe

## Vérifications
* Pas trop de groupes → lorsque tous les groupes sont créés, il faut obligatoirement rejoindre un groupe
* Remplissage du dernier groupe → un utilisateur peut être contraint à rejoindre un groupe
* Placement automatique → en cas de placement automatique, l'utilisateur est soit placé dans un
  groupe non complet, sinon un nouveau groupe est créé si tous les groupes sont complets
* Un groupe ne peut pas avoir moins de 2 utilisateurs sinon il doit être supprimé
* Vérifier la cohérence du modèle → le nombre de groupes est-il correct ?

## Limitations
* A ce stade du projet, on pourra se limiter aux développements suivants :
* Il n'est pas demandé de gérer les comptes des utilisateurs : on pourra se limiter à un simple identifiant
  de connexion sans mot de passe.
* Les groupes peuvent être simplement numérotés groupe 1, groupe 2, …
* Le compte administrateur sera un identifiant 'admin' stocké en dur dans le code

## Améliorations
Une fois les fonctionnalités principales implémentées, on pourra ajouter les suivantes :
* Interface d'administration : l'administrateur possède une vue d'administration sur le programme lui
  permettant de voir la liste des groupes constitués.
* Modification des groupes : l'administrateur peut assigner ou retirer des utilisateurs d'un groupe, détruire
  un groupe existant, créer un groupe et le remplir avec ses utilisateurs
* Ajout ou retrait dynamique d'utilisateurs pendant le fonctionnement du programme :
    * Le nombre total d'utilisateurs change
    * Il faut supprimer l'utilisateur d'un groupe s'il en faisait partie
    * Le nombre total de groupes change
* Exceptions de groupe : l'administrateur peut changer le nombre d'utilisateurs dans un groupe ou forcer
  le nombre de groupes durant l'exécution du programme
* Contraintes de liens : ajouter des métadonnées sur les utilisateurs pour : interdire des utilisateurs d'être
  dans le même groupe, obliger des utilisateurs à être dans le même groupe.
* Création de comptes utilisateurs : les utilisateurs disposent d'un véritable compte, avec un login par
  identifiant / mot de passe.  
  Après étude des besoins du client, vous décidez de développer cette application suivant un processus
  d’intégration continue, afin d’accélérer le développement du projet et pour garantir la qualité des
  fonctionnalités implémentées.

## Travail demandé
Dans le respect du cahier des charges, il est attendu par groupe de 4 :
* La définition d’un pipeline d’intégration continue pour le développement d’une application respectant ce
  cahier des charges (tests unitaires et d’intégration, analyse de code source, création des binaires,
  publication des rapports, …). Il n’est pas demandé de réaliser un déploiement continu (ni génération
  d’artéfacts de production, ni livraison, ni déploiement).
* La mise en place d’un serveur Jenkins correctement administré et réalisant le pipeline d’intégration
  continue défini précédemment. Les différents jobs configurés dans Jenkins devront s’interfacer avec
  les outils d’intégration continue.
* L’application sera testée dans plusieurs environnements, par exemple :
    * Firefox, Google Chrome, Microsoft Edge
* Le développement de l’application en suivant ce processus d’intégration. Le choix des technologies de
  développement est laissé libre.
* La mise en place d’outils de l’intégration continue sur la machine du développeur (tests unitaires,
  analyse statique, formatage du code, …) pour les technologies choisies lors du développement
* L’analyse du code source de l’application dans un serveur SonarQube, et l’intégration des rapports
  dans Jenkins
* L’intégration des rapports des tests automatisés dans Jenkins

Le projet étant réalisé en intégration continue, on sera particulièrement vigilant :
* à utiliser un outil de gestion des versions du code source (git) et à intégrer ses changements le plus
  souvent possible
* à travailler sur des branches git dédiées avant d'intégrer ses changements dans la branche commune
  (**master**, **main**) et à réaliser des revues de code avant d'intégrer les changements
* à penser aux différents tests dès le début du projet, voir à écrire les tests avant l'implémentation du
  code (TDD, BDD)
* un soin tout particulier sera apporté aux tests unitaires qui devront être nombreux

## Rendus attendus
On fournira donc par groupe de 4 :
* Le rapport ci-dessous de l’étude de cas complétée
* Un ensemble de dépôts de code source référencés dans le rapport et contenant les différentes parties
* du projet. Ces dépôts de code devront contenir le code de production ainsi que le code de test.
* Les captures d'écran de revues de code effectuées pour l'intégration de changements dans le projet
* Les captures d'écran de la configuration de Jenkins et des différents jobs et pipelines créés
* Les captures d’écran des résultats d’analyse dans SonarQube
___
## Initialisation du projet

Le projet nécessite une configuration minimale pour pouvoir s'exécuter correctement :
* Au minimum [PHP 7.2](https://www.php.net/) et [Composer](https://getcomposer.org/download/) installé,
* Pointer le vhost vers le dossier `public/` du projet,
* Avoir installé [XDebug](https://xdebug.org/) pour l'exécution des tests

### Installation et mise à jour des dépendances
Installation et mise à jour des dépendances nécessaire au bon fonctionnement du projet :
```shell
composer update
```

### Exécution du projet
Lancement du serveur de production :
```shell
composer start
```

### Tests unitaires et couverture
Exécution des tests unitaire et génération de la couverture de test :
```shell
# Uniquement les tests unitaires
composer test

# Tests unitaire avec couverture
composer test:coverage
```
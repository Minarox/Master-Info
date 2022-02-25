# WPF - Architecture Applicative
Création d'une application web utilisant le modèle de conception MVVM pour visualiser les informations contenu dans un service bus Microsoft Azure.

## Dépendances
- [NodeJS](https://nodejs.org/en/)

## Installation
```shell
npm install
```

## Utilisation
Récupération des messages de la liste BusDepart :
```shell
node busdepart.js
```

Récupération des messages de la liste MsgStock :
```shell
node msgstock.js
```

### Boucles
Le projet possède également des scripts Bash nécessitant Linux permettant de relancer automatiquement la réception des informations des listes.
Pour les utiliser, vous devez modifier les permissions des fichiers pour exécuter les scripts :
```shell
# Modification des permissions d'exécution
chmod 755 busdepart.sh msgstock.sh

# Boucle pour BusDepart
./busdepart.sh

# Boucle pour MsgStock
./msgstock.sh
```
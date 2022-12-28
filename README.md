# SYMFONY-laboutique-dynamique
Projet d'une boutique fictive très dynamique

# Description

Ce projet a pour but d'appliquer toutes les connaissances nécessaires
pour mener à bien la réalisation totale d'un site de e-commerce avec l'utilisation du framework Symfony.
Je réalise ce projet durant mes études en 3 ème année de licence Informatique.
Une fois terminé il sera presenté dans mon futur portfolio.

# Commandes

Je me répertorie toutes les commandes qui m'ont servis à réaliser ce projet :

## Création du projet :
```
$ symfony new --full nom-du-projet
```

## Création de la base de données / tables :

On créer d'abord le fichier .env.local que l'on configure suivant nos versions

#### Création de la base de données
```
$ symfony console doctrine:databse:create
```
#### Création d'une entity user 
```
$ symfony console make:user
```
#### Création d'une entity
```
$ symfony console make:entity
```
#### Création de la migration
```
$ symfony console make:migration
```
#### Exécution de la migration
```
$ symfony console doctrine:migrations:migrate ou d:m:m
```

## Ajout de tailwindcss :

#### 1.Installation :
```
$ npm install -D tailwindcss
$ npx tailwindcss init
```
#### 2.Configuration :
On configure le content du fichier tailwind.config.js
#### 3.On import dans le fichier css d'entrée :
```
@tailwind base;
@tailwind components;
@tailwind utilities;
```
#### 4.On lance la construction du fichier d'entrée vers celui de sortie :
```
$ npx tailwindcss -i ./src/input.css -o ./dist/output.css --watch
```
#### 5.On oublie pas d'ajouter le link du fichier de sortie







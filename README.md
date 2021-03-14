# API Sécurisé 

## Api Léonard de Vinci

L'Api Léonard de Vinci permet de créer/modifier/supprimer/voir :
* Une Classe
* Un Eleve
* Un Intervenant
* Une Matière
* Une Note
---
## Installation 

### Initialisation

Après avoir installer le projet Symfony en local,
Executer, dans un terminale, la commande suivante :
```bash
composer install
```

### Création de la base de données

Une fois tous les packages installés:
* Il faut créer un nouveau fichier `.env.local` à la **racine** du projet.
* **Copier/Coller**  la totalité du contenu du fichier `.env` dans `.env.local`
* Commenter la ligne 27 :  
   `DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=13&charset=utf8"`
* Remplacer la ligne 26 par la suivante (vous pouvez aussi la modifier comme bon vous semble) :   
    `DATABASE_URL="mysql://root:@127.0.0.1:3306/ldv_api_sharcan?serverVersion=5.7"`

Executer la commande suivante :
```bash
php bin/console doctrine:database:create
```
La base de données est normalement créée !

### Colonisation

Il faut maintenant créer les différentes entités que nous allons utiliser pour peupler la base.  
Pour ceci exécuter la commande suivante :
```bash
php bin/console doctrine:migrations:migrate
```
Une fois terminé, nous pouvons enfin procéder à la colonisation de la base :
```bash
php bin/console doctrine:fixture:load
```

Nous venons de créer et peupler différentes entités, qui sont:
* Classe
* Etudiant
* Intervenant
* Matiere
* Note
* Admin

### Installation de JWT

Enfin il faut rajouter les fichiers de configuration du package JWT.
Pour se faire, il faut créer un dossier jwt dans le dossier config du projet :
```bash
mkdir config/jwt
```
Executer la commande suivante à la racine du projet:
```bash
openssl genrsa -out config/jwt/private.pem -aes256 4096
```
Indiquer un mot de passe lorsque la console le demandera.

Ensuite : 
```bash
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
Indiquer le même mot de passe.

Retourner dans le fichier `.env.local` et mettre le mot de passe indiquer avant sur la ligne **JWT_PASSPHRASE**

## Lancement
Le projet est enfin prêt à être lancé:
```bash
symfony server:start
```

---

## Utilisation de l'API

Dans un premier temps, il est possible de se rendre à l'adresse suivante pour avoir accès à toutes les routes disponibles :  
[http://localhost:8000/api/doc](http://localhost:8000/api/doc)

*NB:* Il est malheureusement impossible d'ajouter ou de modifier une entité via l'API Doc (Impossible de configurer le PhpDoc pour le permettre).

Dans un second temps, il est possible de retrouver toutes les requetes que l'on peut importer dans **Postman** dans le fichier `ldv-api-sharcan.postman_collection.json`.  

### Requetes disponibles

Pour accéder aux requêtes ci-dessous il faut **obligatoirement** avoir un Token JWT.
* Pour se faire, l'url `http://localhost:8000/api/login/` est disponible en méthode **POST**
    * email disponible : *Karine@devinci.fr*, *Alexis@devinci.fr*, *Nicolas@devinci.fr*
    * password : "password"
    
Réponse retour :

```json
{
  "token": "string"
}
```

Il faut ensuite ensuite pour chaque requête rajouter dans le header le Paramètre :
`Authorization: Bearer {token}`

Chaque entité peut être:
* Récupérée par l'url suivante (en méthode **"GET"**):  
`http://localhost:8000/api/{NomEntite}/`  
Il est possible de rajouter l'id pour récupérer une donnée précise  
`http://localhost:8000/api/{NomEntite}/{id}`  
  
* Supprimée par l'url suivante (en méthode **"DELETE"**):  
  `http://localhost:8000/api/{NomEntite}/{id}`  
  
* Rajoutée par l'url suivante (en méthode **"POST"**):  
  `http://localhost:8000/api/{NomEntite}/`  
  * En ajoutant les variables qui correspondent dans le body.

* Modifiée par l'url suivante (en méthode **"PUT"**):  
  `http://localhost:8000/api/{NomEntite}/{id}`
    * En ajoutant les variables qui correspondent dans le body.


### Propriété pour chaque entité:

* Classe :
```json
{
  "name": "string",
  "annee": "string"
}
```
* Etudiant :
```json
{
  "nom": "string",
  "prenom": "string",
  "age": 0,
  "annee": "string",
  "classe_id": 0
}
```
* Intervenant :
```json
{
  "nom": "string",
  "prenom": "string",
  "annee": "string"
}
```
* Matiere :
```json
{
  "nom": "string",
  "debut": "string",
  "fin": "string",
  "classe": 0,
  "intervenant": 0
}
```
* Note :
```json
{
  "note": 0,
  "etudiant": 0,
  "matiere": 0
}
```
---

Nicolas Brazzolotto *dit* Sharcan.

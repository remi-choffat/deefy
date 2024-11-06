# Projet Deefy
<img src="/resources/logo.png" alt="Deefy" width="60" height="60">

L'application Deefy est un service de streaming pour l'écoute de musique. Elle s'inspire de l'application Spotify. Les utilisateurs enregistrés peuvent créer des playlists composées de pistes qu'ils ont importées.

> <i>Réalisé par [Rémi Choffat](https://github.com/remi-choffat) et [Nathan Viry](https://github.com/Njajv)</i>

## Fonctionnalités
- [X] Affichage de la liste des playlists de l’utilisateur authentifié ; chaque élément de la
liste est cliquable et permet d’afficher une playlist qui devient la playlist courante
- [X] Création d'une playlist (vide) : un formulaire permettant de saisir le nom d’une nouvelle playlist est
affiché. A la validation, la playlist est créée et stockée dans la base de données ; elle devient la playlist
courante.
- [X] Affichage de la playlist courante (stockée en session),
- [X] Inscription : création d’un compte utilisateur
- [X] Authentification : Un utilisateur enregistré peut se connecter à l'application
- [X] L'affichage d’une playlist propose toujours d’ajouter une nouvelle piste à la playlist. Le formulaire
de saisie des données de description d’une piste est affiché. A la validation, la piste est créée et
enregistrée dans la base puis ajoutée à la playlist affichée.
- [X] L'affichage d’une playlist est réservé au propriétaire de la playlist
### Fonctionnalités supplémentaires
- [X] Lorsqu'une action n'est pas possible (utilisateur non autorisé, playlist non stockée, liste vide), on avertit l'utilisateur avant qu'il tente d'effectuer l'action, en lui proposant une alternative (par exemple, créer une playlist avant d'ajouter des pistes).
- [X] On propose à un utilisateur non authentifié uniquement les actions qu'il peut effectuer sans générer d'erreur.
- [X] L'utilisateur a un pseudo / nom (colonne ajoutée en base de données) pour un meilleur rendu.
- [X] Un utilisateur peut se déconnecter, ce qui efface les données en session, et peut permettre à un autre utilisateur de s'authentifier.

## Éléments permettant le test de l'application
### Connexion à une base de données
Le fichier `exemple.config.db.ini`peut être copié en `config.db.ini`, dans lequel on fournira les informations de connexion à la base de données.
### Identifiants d'utilisateurs de test
Il est possible de se connecter au compte d'utilisateurs déjà existants dans le but de tester l'application. Voici leurs identifiants :
- `user1@mail.com`: `user1`
- `user2@mail.com`: `user2`
- `user3@mail.com`: `user2`  

Les pistes des playlists de ces utilisateurs ne sont pas écoutables. C'est pourquoi je vous invite à créer votre propre compte et/ou à ajouter vos propres pistes.  

> **Important** : Si un problème venait à survenir lors de l'exécution de l'application, vous pouvez y accéder depuis [
_Webetu_](https://webetu.iutnc.univ-lorraine.fr/www/choffat2u/S3/Web/deefy/) (les données utilisées seront alors
> celles de ma base de données sur Webetu).

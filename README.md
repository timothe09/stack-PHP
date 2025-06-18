# 🐳 Stack PHP + MySQL avec Docker (Nginx)

Stack de développement complète avec PHP-FPM, Nginx, MySQL, PhpMyAdmin et Redis pour le développement local.

## 📋 Prérequis

- Docker
- Docker Compose

## 🚀 Démarrage rapide

1. **Cloner ou télécharger ce projet**
2. **Démarrer la stack :**
   ```bash
   docker-compose up -d
   ```

3. **Accéder aux services :**
   - **Application PHP :** http://localhost:8080
   - **PhpMyAdmin :** http://localhost:8081
   - **MySQL :** localhost:3306
   - **Redis :** localhost:6379

## 📁 Structure du projet

```
├── docker-compose.yml      # Configuration des services
├── Dockerfile             # Image PHP personnalisée
├── src/                   # Code source PHP
│   └── index.php         # Page de test
├── config/
│   ├── php/
│   │   └── php.ini       # Configuration PHP
│   └── mysql/
│       └── init.sql      # Script d'initialisation MySQL
└── README.md             # Ce fichier
```

## 🔧 Services inclus

### PHP-FPM
- **Version :** PHP 8.2-FPM
- **Extensions :** mysqli, pdo_mysql, gd, zip
- **Composer :** Inclus
- **Répertoire de travail :** `/var/www/html` (mappé sur `./src`)

### Nginx
- **Version :** Nginx Alpine
- **Port :** 8080
- **Configuration :** Optimisée pour PHP-FPM

### MySQL
- **Version :** MySQL 8.0
- **Port :** 3306
- **Base de données :** `app_database`
- **Utilisateur :** `app_user`
- **Mot de passe :** `app_password`
- **Root password :** `root_password`

### PhpMyAdmin
- **Port :** 8081
- **Utilisateur :** `app_user` ou `root`
- **Mot de passe :** `app_password` ou `root_password`

### Redis
- **Version :** Redis 7 Alpine
- **Port :** 6379
- **Persistance :** Activée

## 💾 Données persistantes

Les données MySQL et Redis sont stockées dans des volumes Docker nommés :
- `mysql_data` : Données MySQL
- `redis_data` : Données Redis

## 🛠️ Commandes utiles

### Démarrer la stack
```bash
docker-compose up -d
```

### Arrêter la stack
```bash
docker-compose down
```

### Voir les logs
```bash
# Tous les services
docker-compose logs -f

# Service spécifique
docker-compose logs -f php
docker-compose logs -f mysql
```

### Reconstruire les images
```bash
docker-compose build --no-cache
docker-compose up -d
```

### Accéder au conteneur PHP
```bash
docker-compose exec php bash
```

### Accéder au conteneur MySQL
```bash
docker-compose exec mysql mysql -u app_user -p app_database
```

### Sauvegarder la base de données
```bash
docker-compose exec mysql mysqldump -u root -proot_password app_database > backup.sql
```

### Restaurer la base de données
```bash
docker-compose exec -T mysql mysql -u root -proot_password app_database < backup.sql
```

## 🔍 Configuration

### Variables d'environnement
Modifiez les variables dans le [`docker-compose.yml`](docker-compose.yml:1) :

```yaml
environment:
  MYSQL_ROOT_PASSWORD: root_password
  MYSQL_DATABASE: app_database
  MYSQL_USER: app_user
  MYSQL_PASSWORD: app_password
```

### Configuration PHP
Modifiez le fichier [`config/php/php.ini`](config/php/php.ini:1) selon vos besoins.

### Script d'initialisation MySQL
Le fichier [`config/mysql/init.sql`](config/mysql/init.sql:1) est exécuté automatiquement lors du premier démarrage.

## 🚨 Dépannage

### Les conteneurs ne démarrent pas
```bash
docker-compose logs
```

### Erreur de connexion MySQL
1. Vérifiez que le conteneur MySQL est démarré : `docker-compose ps`
2. Attendez quelques secondes après le démarrage initial
3. Vérifiez les logs MySQL : `docker-compose logs mysql`

### Erreur de permissions
```bash
sudo chown -R $USER:$USER ./src
```

### Réinitialiser complètement
```bash
docker-compose down -v
docker-compose up -d
```

## 📝 Développement

1. Placez votre code PHP dans le répertoire `src/`
2. Les fichiers sont automatiquement synchronisés avec le conteneur
3. Aucun redémarrage nécessaire pour les modifications PHP

## 🔐 Sécurité

⚠️ **Cette configuration est destinée au développement uniquement !**

Pour la production :
- Changez tous les mots de passe
- Configurez des certificats SSL
- Limitez l'exposition des ports
- Activez les logs de sécurité
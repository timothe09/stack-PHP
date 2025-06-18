-- Script d'initialisation MySQL pour la stack de développement
-- Ce fichier est exécuté automatiquement lors du premier démarrage du conteneur MySQL

-- Création de tables d'exemple
USE app_database;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des articles/posts
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    user_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table de liaison posts-catégories
CREATE TABLE IF NOT EXISTS post_categories (
    post_id INT,
    category_id INT,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Insertion de données de test
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('developer', 'dev@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO categories (name, description) VALUES
('Développement', 'Articles sur le développement web'),
('PHP', 'Tutoriels et astuces PHP'),
('MySQL', 'Base de données et requêtes SQL'),
('Docker', 'Conteneurisation et DevOps');

INSERT INTO posts (title, content, user_id, status) VALUES
('Bienvenue dans votre stack PHP + MySQL', 'Ceci est votre premier article de test. Votre environnement de développement est maintenant prêt !', 1, 'published'),
('Configuration Docker', 'Guide de configuration de votre environnement Docker pour PHP et MySQL.', 2, 'published'),
('Tips PHP', 'Quelques astuces pour améliorer votre code PHP.', 2, 'draft');

INSERT INTO post_categories (post_id, category_id) VALUES
(1, 1), (1, 2),
(2, 1), (2, 4),
(3, 2);

-- Création d'un utilisateur MySQL supplémentaire pour les tests
CREATE USER IF NOT EXISTS 'test_user'@'%' IDENTIFIED BY 'test_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON app_database.* TO 'test_user'@'%';

-- Affichage des informations de la base
SELECT 'Base de données initialisée avec succès !' as message;
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_posts FROM posts;
SELECT COUNT(*) as total_categories FROM categories;

FLUSH PRIVILEGES;
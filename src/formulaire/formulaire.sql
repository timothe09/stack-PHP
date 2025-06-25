CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(100),
    description TEXT,
    date_enregistrement TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
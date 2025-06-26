-- Insertion du jeu Carré Surfers
INSERT INTO games (title) VALUES ('Carré Surfers')
ON DUPLICATE KEY UPDATE title = title;
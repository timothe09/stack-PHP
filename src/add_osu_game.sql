-- Insertion du jeu OSU!
INSERT INTO games (title) VALUES ('OSU!')
ON DUPLICATE KEY UPDATE title = title;
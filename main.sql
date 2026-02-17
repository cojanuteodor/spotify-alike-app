#source C:/wamp64/www/site/bd.sql;



DROP DATABASE IF EXISTS spotifyDB;
CREATE DATABASE spotifyDB;
USE spotifyDB;


-- 1. UTILIZATORI
CREATE TABLE Utilizatori_tbl (
    id_utilizator INT PRIMARY KEY,
    nume_utilizator VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    parola VARCHAR(100) NOT NULL,
    data_inregistrare DATE NOT NULL,
    tara VARCHAR(50)
);

-- 2. ARTISTI
CREATE TABLE Artisti_tbl (
    id_artist INT PRIMARY KEY,
    nume VARCHAR(100) NOT NULL,
    gen_muzical VARCHAR(50),
    tara_origine VARCHAR(50),
    UNIQUE(nume)
);

-- 3. ALBUME
CREATE TABLE Albume_tbl (
    id_album INT PRIMARY KEY,
    titlu VARCHAR(100) NOT NULL,
    id_artist INT,
    an_lansare INT,
    FOREIGN KEY (id_artist) REFERENCES Artisti_tbl(id_artist) ON DELETE CASCADE
);

-- 4. PIESE (fără id_artist)
CREATE TABLE Piese_tbl (
    id_piesa INT PRIMARY KEY,
    titlu VARCHAR(100) NOT NULL,
    durata_secunde INT,
    id_album INT,
    FOREIGN KEY (id_album) REFERENCES Albume_tbl(id_album) ON DELETE SET NULL
);

-- 5. PLAYLISTURI
CREATE TABLE Playlisturi_tbl (
    id_playlist INT AUTO_INCREMENT PRIMARY KEY,
    id_utilizator INT,
    titlu VARCHAR(100) NOT NULL,
    data_creare DATE,
    FOREIGN KEY (id_utilizator) REFERENCES Utilizatori_tbl(id_utilizator) ON DELETE CASCADE
);




-- 6. PIESE_PLAYLIST (many-to-many)
DROP TABLE IF EXISTS PiesePlaylist_tbl;

CREATE TABLE PiesePlaylist_tbl (
    id_playlist INT,
    id_piesa INT,
    data_adaugare DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_playlist, id_piesa),
    FOREIGN KEY (id_playlist) REFERENCES Playlisturi_tbl(id_playlist) ON DELETE CASCADE,
    FOREIGN KEY (id_piesa) REFERENCES Piese_tbl(id_piesa) ON DELETE CASCADE
);





-- UTILIZATORI
INSERT INTO Utilizatori_tbl VALUES
(1, 'ion_popescu', 'ion@example.com', 'parola123', '2021-01-15', 'România'),
(2, 'ana_maria', 'ana@example.com', 'superparola', '2022-03-10', 'România'),
(3, 'fan_kpop', 'kpop@example.com', 'btsforever', '2023-05-20', 'Coreea de Sud'),
(4, 'alex_rock', 'alex@example.com', 'rock123', '2020-06-10', 'Germania'),
(5, 'sara90', 'sara@example.com', 'sara1234', '2019-12-01', 'Italia'),
(6, 'mihai_jazz', 'mihai@example.com', 'jazzpass', '2023-08-12', 'România'),
(7, 'lena_muzica', 'lena@example.com', 'melodie88', '2024-02-14', 'Spania'),
(8, 'daniel_trap', 'daniel@example.com', 'trapgame', '2022-11-30', 'România'),
(9, 'carla_bass', 'carla@example.com', 'base123', '2023-04-09', 'Franța'),
(10, 'george_classic', 'george@example.com', 'mozartfan', '2021-07-25', 'Austria');

-- ARTISTI
INSERT INTO Artisti_tbl VALUES
(1, 'Taylor Swift', 'Pop', 'SUA'),
(2, 'Ed Sheeran', 'Pop', 'UK'),
(3, 'BTS', 'K-pop', 'Coreea de Sud'),
(4, 'Imagine Dragons', 'Rock', 'SUA'),
(5, 'Adele', 'Pop/Soul', 'UK'),
(6, 'The Weeknd', 'R&B', 'Canada'),
(7, 'Drake', 'Rap', 'Canada'),
(8, 'Coldplay', 'Alternative', 'UK'),
(9, 'Dua Lipa', 'Pop', 'UK'),
(10, 'Mozart', 'Clasic', 'Austria');

-- ALBUME
INSERT INTO Albume_tbl VALUES
(1, '1989', 1, 2014),
(2, 'Divide', 2, 2017),
(3, 'Map of the Soul: 7', 3, 2020),
(4, 'Night Visions', 4, 2012),
(5, '25', 5, 2015),
(6, 'After Hours', 6, 2020),
(7, 'Scorpion', 7, 2018),
(8, 'Music of the Spheres', 8, 2021),
(9, 'Future Nostalgia', 9, 2020),
(10, 'Requiem', 10, 1791);

-- PIESE
INSERT INTO Piese_tbl VALUES
(1, 'Blank Space', 272, 1),
(2, 'Style', 243, 1),
(3, 'Shape of You', 263, 2),
(4, 'Perfect', 279, 2),
(5, 'ON', 354, 3),
(6, 'Black Swan', 217, 3),
(7, 'Radioactive', 261, 4),
(8, 'Demons', 236, 4),
(9, 'Hello', 366, 5),
(10, 'When We Were Young', 342, 5),
(11, 'Blinding Lights', 262, 6),
(12, 'Save Your Tears', 248, 6),
(13, 'God\'s Plan', 356, 7),
(14, 'Nice for What', 262, 7),
(15, 'Higher Power', 256, 8),
(16, 'My Universe', 282, 8),
(17, 'Don\'t Start Now', 181, 9),
(18, 'Levitating', 230, 9),
(19, 'Lacrimosa', 200, 10),
(20, 'Dies Irae', 113, 10);

-- PLAYLISTURI
INSERT INTO Playlisturi_tbl VALUES
(1, 1, 'Dimineti Energice', '2024-01-01'),
(2, 2, 'K-pop Preferate', '2024-02-20'),
(3, 3, 'Antrenament', '2024-03-15'),
(4, 4, 'Rock Session', '2024-01-10'),
(5, 5, 'Relaxare Seara', '2024-01-11'),
(6, 6, 'Jazz Vibes', '2024-01-12'),
(7, 7, 'Party Hits', '2024-01-13'),
(8, 8, 'Trap Mood', '2024-01-14'),
(9, 9, 'Bass Boosted', '2024-01-15'),
(10, 10, 'Muzica Clasica', '2024-01-16');

-- PIESE_PLAYLIST
INSERT INTO PiesePlaylist_tbl VALUES
(1, 1, '2024-01-01'),
(1, 3, '2024-01-01'),
(2, 5, '2024-02-21'),
(2, 6, '2024-02-21'),
(3, 3, '2024-03-16'),
(3, 4, '2024-03-16'),
(3, 5, '2024-03-16'),
(4, 7, '2024-01-10'),
(4, 8, '2024-01-10'),
(5, 9, '2024-01-11'),
(5, 10, '2024-01-11'),
(6, 11, '2024-01-12'),
(6, 12, '2024-01-12'),
(7, 17, '2024-01-13'),
(7, 18, '2024-01-13'),
(8, 13, '2024-01-14'),
(8, 14, '2024-01-14'),
(9, 7, '2024-01-15'),
(9, 13, '2024-01-15'),
(10, 19, '2024-01-16'),
(10, 20, '2024-01-16');




DESCRIBE Utilizatori_tbl;
DESCRIBE Artisti_tbl;
DESCRIBE Albume_tbl;
DESCRIBE Piese_tbl;
DESCRIBE Playlisturi_tbl;
DESCRIBE PiesePlaylist_tbl;

SELECT * FROM Utilizatori_tbl;
SELECT * FROM Artisti_tbl;
SELECT * FROM Albume_tbl;
SELECT * FROM Piese_tbl;
SELECT * FROM Playlisturi_tbl;
SELECT * FROM PiesePlaylist_tbl;


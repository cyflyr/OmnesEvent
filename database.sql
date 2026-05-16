-- Création de la base de données OmnesEvent
CREATE DATABASE IF NOT EXISTS omnesevent CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE omnesevent;

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('participant', 'organisateur', 'administrateur') DEFAULT 'participant',
    valide TINYINT(1) DEFAULT 1,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des événements
CREATE TABLE evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    date_event DATE NOT NULL,
    heure TIME NOT NULL,
    lieu VARCHAR(150) NOT NULL,
    categorie ENUM('soiree', 'sport', 'culture', 'conference') NOT NULL,
    association VARCHAR(100),
    affiche VARCHAR(255),
    capacite_max INT NOT NULL,
    organisateur_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organisateur_id) REFERENCES utilisateurs(id)
);

-- Table des inscriptions aux événements
CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    evenement_id INT NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('inscrit', 'annule') DEFAULT 'inscrit',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (evenement_id) REFERENCES evenements(id),
    present TINYINT(1) DEFAULT 0,
    UNIQUE(utilisateur_id, evenement_id)
);

-- Compte administrateur par défaut (mot de passe : admin123)
INSERT INTO utilisateurs (nom_utilisateur, password, prenom, nom, email, role, valide) VALUES
('admin', '$2y$10$fF0Yispku6/dCkIS3cfo8OAZkddJPjPQg8Z.BOdkbITPOYtxXXoMy', 'Admin', 'OmnesEvent', 'admin@omnesevent.fr', 'administrateur', 1);

-- Quelques événements de test
INSERT INTO evenements (titre, description, date_event, heure, lieu, categorie, association, capacite_max, organisateur_id) VALUES
('Gala de fin d\'annee', 'La grande soiree de fin d\'annee organisee par le BDE. Dress code : chic !', '2026-06-15', '21:00:00', 'Salle des fetes, Campus Omnes', 'soiree', 'BDE', 250, 1),
('Tournoi Inter-Ecoles Football', 'Tournoi de football entre les differentes ecoles du groupe Omnes.', '2026-06-22', '14:00:00', 'Terrain sportif, Puteaux', 'sport', 'BDS', 80, 1),
('Conference IA et Sante', 'Decouvrez comment l\'intelligence artificielle transforme le monde de la sante.', '2026-06-28', '10:00:00', 'Amphitheatre B, Campus Paris', 'conference', 'Junior Entreprise', 120, 1),
('Soiree Quiz Culture Generale', 'Testez vos connaissances lors de cette soiree quiz conviviale !', '2026-07-05', '19:00:00', 'Cafeteria, Campus Omnes', 'culture', 'BDE', 60, 1),
('Marathon de Programmation', 'Hackathon de 12h pour developper un projet innovant en equipe.', '2026-07-12', '08:00:00', 'Salle informatique, Batiment C', 'culture', 'Junior Entreprise', 40, 1);

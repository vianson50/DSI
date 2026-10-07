-- =====================================================
-- Base de données du site DSI
-- À importer via phpMyAdmin ou : mysql -u root -p < database.sql
-- =====================================================

CREATE DATABASE IF NOT EXISTS dsi_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dsi_site;

-- -----------------------------------------------------
-- Table : services
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT 'assets/img/service-1.jpg',
    icone VARCHAR(50) DEFAULT 'code',
    actif TINYINT(1) NOT NULL DEFAULT 1,
    ordre_affichage INT NOT NULL DEFAULT 0,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table : messages (formulaire de contact)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(30) DEFAULT NULL,
    sujet VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table : administrateurs
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table : ordinateurs (boutique de vente de PC)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS ordinateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10,2) DEFAULT NULL,
    devise VARCHAR(10) NOT NULL DEFAULT 'FCFA',
    image VARCHAR(255) NOT NULL DEFAULT 'assets/img/pc/pc-1.jpg',
    actif TINYINT(1) NOT NULL DEFAULT 1,
    ordre_affichage INT NOT NULL DEFAULT 0,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table : demandes_devis (demandes envoyées depuis le site)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS demandes_devis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    telephone VARCHAR(30) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    service VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    traitee TINYINT(1) NOT NULL DEFAULT 0,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table : devis (devis créés par l'administrateur)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS devis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(20) NOT NULL UNIQUE,
    client_nom VARCHAR(150) NOT NULL,
    client_telephone VARCHAR(30) NOT NULL,
    client_email VARCHAR(150) DEFAULT NULL,
    objet VARCHAR(200) DEFAULT NULL,
    statut ENUM('brouillon','envoye','accepte','refuse') NOT NULL DEFAULT 'brouillon',
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table : devis_lignes (lignes de prestations d'un devis)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS devis_lignes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    devis_id INT NOT NULL,
    designation VARCHAR(255) NOT NULL,
    quantite DECIMAL(10,2) NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(12,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Données initiales
-- Mot de passe par défaut : admin123 (À CHANGER !)
-- -----------------------------------------------------
INSERT INTO admins (nom_utilisateur, mot_de_passe_hash)
VALUES ('admin', '$2y$12$O..ixuYGyVthGIYTxOLMiu3fUT4dS8PBQZYBmFhCYR7L7RgBeI3LC')
ON DUPLICATE KEY UPDATE nom_utilisateur = nom_utilisateur;

INSERT INTO services (titre, description, image, icone, ordre_affichage) VALUES
('Installation de caméras de surveillance', 'Étude, installation et configuration de systèmes de vidéosurveillance pour sécuriser vos locaux, domicile ou entreprise.', 'assets/img/service-1.jpg', 'camera', 1),
('Vente d''ordinateurs & accessoires informatiques', 'Large gamme d''ordinateurs, portables et périphériques : sélection fiable au meilleur rapport qualité-prix.', 'assets/img/service-2.jpg', 'laptop', 2),
('Maintenance informatique', 'Entretien et maintenance préventive de vos équipements pour des performances durables et une disponibilité maximale.', 'assets/img/service-1.jpg', 'wrench', 3),
('Vente d''accessoires de téléphone', 'Chargeurs, coques, protections d''écran, écouteurs et tous les accessoires pour votre smartphone.', 'assets/img/service-2.jpg', 'phone', 4),
('Diagnostic et devis', 'Diagnostic complet et gratuit de votre matériel avec un devis clair et transparent avant toute intervention.', 'assets/img/service-1.jpg', 'clipboard', 5),
('Dépannage système / nettoyage de virus', 'Élimination des virus et logiciels malveillants, optimisation et réparation de votre système d''exploitation.', 'assets/img/service-2.jpg', 'shield', 6),
('Réinstallation de système', 'Réinstallation complète de Windows ou de votre système avec récupération de vos données importantes.', 'assets/img/service-1.jpg', 'disk', 7),
('Changement de pièces (écran, clavier, SSD)', 'Remplacement de composants défectueux : écrans, claviers, disques SSD et autres pièces pour ordinateurs portables et fixes.', 'assets/img/service-2.jpg', 'cpu', 8),
('Installation et configuration d''imprimante', 'Installation, connexion réseau et configuration de vos imprimantes, en bureau comme à domicile.', 'assets/img/service-1.jpg', 'printer', 9),
('Création de cartes de visite', 'Conception de cartes de visite professionnelles et personnalisées pour valoriser votre image de marque.', 'assets/img/service-2.jpg', 'card', 10);

INSERT INTO ordinateurs (nom, description, prix, image, ordre_affichage) VALUES
('Ordinateur portable 1', 'À compléter depuis l''espace admin : caractéristiques techniques du PC (processeur, mémoire, stockage…).', NULL, 'assets/img/pc/pc-1.jpg', 1),
('Ordinateur portable 2', 'À compléter depuis l''espace admin : caractéristiques techniques du PC (processeur, mémoire, stockage…).', NULL, 'assets/img/pc/pc-2.jpg', 2),
('Ordinateur portable 3', 'À compléter depuis l''espace admin : caractéristiques techniques du PC (processeur, mémoire, stockage…).', NULL, 'assets/img/pc/pc-3.jpg', 3),
('Ordinateur portable 4', 'À compléter depuis l''espace admin : caractéristiques techniques du PC (processeur, mémoire, stockage…).', NULL, 'assets/img/pc/pc-4.jpg', 4),
('Ordinateur portable 5', 'À compléter depuis l''espace admin : caractéristiques techniques du PC (processeur, mémoire, stockage…).', NULL, 'assets/img/pc/pc-5.jpg', 5),
('Ordinateur portable 6', 'À compléter depuis l''espace admin : caractéristiques techniques du PC (processeur, mémoire, stockage…).', NULL, 'assets/img/pc/pc-6.jpg', 6);

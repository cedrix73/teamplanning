-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET GLOBAL sql_mode='';
SET time_zone = "+00:00";


--
-- Base de données: `team_planning`
--
CREATE DATABASE IF NOT EXISTS `team_planning` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `team_planning`;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS site (
  id integer NOT NULL AUTO_INCREMENT,
  libelle varchar(50),
  description varchar (255) DEFAULT NULL,
  PRIMARY KEY (id)
);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS departement (
  id integer NOT NULL AUTO_INCREMENT,
  libelle varchar(50),
  description varchar(255) DEFAULT NULL,
  site_id integer not null,
  KEY site_idx (site_id),
  CONSTRAINT site_fk FOREIGN KEY (site_id)
        REFERENCES site(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
  PRIMARY KEY (id)
);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS service (
  id integer NOT NULL AUTO_INCREMENT,
  libelle varchar(50),
  departement_id integer NOT NULL,
  description varchar(255) DEFAULT NULL,
  KEY departement_idx (departement_id),
  CONSTRAINT departement_fk FOREIGN KEY (departement_id)
        REFERENCES departement(id)
        ON UPDATE CASCADE 
        ON DELETE CASCADE,
  PRIMARY KEY (id)
);

-- --------------------------------------------------------

--
-- Structure de la table `ressource`
--

CREATE TABLE IF NOT EXISTS ressource (
  id integer NOT NULL AUTO_INCREMENT,
  NNI varchar(45) DEFAULT NULL,
  nom varchar(255) NOT NULL,
  prenom varchar(255) NOT NULL,
  telephone varchar(20) DEFAULT NULL,
  adresse_mail varchar(255) NOT NULL,
  mot_de_passe varchar(255),
  date_naissance date DEFAULT NULL,
  site_id integer(255) not NULL,
  departement_id integer(255) not NULL,
  service_id integer not NULL,
  bureau varchar(45) DEFAULT NULL,
  statut varchar(45) DEFAULT NULL,
  num_badge varchar(255) DEFAULT NULL,
  date_signature_charte date DEFAULT NULL,
  date_sensibilitation_doc date DEFAULT NULL,
  date_entree date NOT NULL,
  date_sortie date DEFAULT NULL,
  date_restitution_materiel varchar(45) DEFAULT NULL,
  date_restitution_badge date DEFAULT NULL,
  commentaire text DEFAULT NULL,
  num_secur_id varchar(10) DEFAULT NULL,
  is_admin char(1) DEFAULT NULL,
  KEY departement_idx (departement_id),
  CONSTRAINT ressource_site_fk FOREIGN KEY (site_id)
        REFERENCES site(id)
        ON UPDATE CASCADE 
        ON DELETE CASCADE,
  CONSTRAINT ressource_departement_fk FOREIGN KEY (departement_id)
        REFERENCES departement(id)
        ON UPDATE CASCADE 
        ON DELETE CASCADE,
  CONSTRAINT ressource_service_fk FOREIGN KEY (service_id)
        REFERENCES service(id)
        ON UPDATE CASCADE 
        ON DELETE CASCADE,
  PRIMARY KEY (id)
);


-- Structure de la table `event`
--

CREATE TABLE IF NOT EXISTS evenement (
  id integer NOT NULL AUTO_INCREMENT,
  libelle varchar(20) NOT NULL,
  affichage varchar(3) NOT NULL,
  couleur varchar(6) NOT NULL DEFAULT 'CCFF66',
  PRIMARY KEY (id)
);

--
-- Contenu de la table `event`
--

INSERT INTO evenement (libelle, affichage, couleur) VALUES 
('Télétravail', 'Tel', 'FFFFCC'),
('CP', 'CP', '6600CC'),
('RTT', 'RTT', 'CC0033'),
('Formation', 'Crs', '999966'),
('Malade', 'Mde', '339933'),
('Déplacement', 'Dpl', '953734');

-- --------------------------------------------------------


--
-- Structure de la table `planning`
--

CREATE TABLE IF NOT EXISTS planning (
  event integer NOT NULL,
  ressource integer NOT NULL,
  jour date NOT NULL,
  periode TINYINT(1) NOT NULL COMMENT '1:journée, 2=matin, 3=AM',
  PRIMARY KEY (ressource,jour)
);

-- --------------------------------------------------------
--
-- Structure de la table `feries`
--

CREATE TABLE IF NOT EXISTS feries (
  id integer NOT NULL AUTO_INCREMENT,
  libelle varchar(20) NOT NULL,
  actif smallint NOT NULL,
  PRIMARY KEY (id)
);

--
-- Contenu de la table `feries`
--

INSERT INTO feries (libelle, actif) VALUES
('Fête du travail', 1),
('Toussaint', 1),
('Mardi Gras', 1),
('Epiphanie', 1),
('Victoire 1945', 1),
('Pentecôte', 1),
('Lundi de Pentecôte', 1),
('Armistice de 1918', 1),
('Saint Valentin', 1),
('Fête Nationale', 1),
('Assomption', 1),
('Pâques', 1),
('Lundi de Pâques', 1),
('Noël', 1),
('Ascension', 1),
('Saint-Sylvestre', 1),
('Premier de l''An', 1);



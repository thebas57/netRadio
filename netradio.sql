-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 09 avr. 2020 à 17:28
-- Version du serveur :  5.7.26
-- Version de PHP :  7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `netradio`
--
CREATE DATABASE IF NOT EXISTS `netradio` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `netradio`;

-- --------------------------------------------------------

--
-- Structure de la table `creneau`
--

DROP TABLE IF EXISTS `creneau`;
CREATE TABLE IF NOT EXISTS `creneau` (
  `creneau_id` int(50) NOT NULL AUTO_INCREMENT,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `date_creneau` date NOT NULL,
  `emission_id` int(50) DEFAULT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`creneau_id`),
  KEY `emission_id` (`emission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `creneau`
--

INSERT INTO `creneau` (`creneau_id`, `heure_debut`, `heure_fin`, `date_creneau`, `emission_id`, `deleted_at`) VALUES
(17, '20:00:00', '23:00:00', '2020-04-02', 1, NULL),
(18, '12:00:00', '13:00:00', '2020-04-07', 2, NULL),
(19, '07:00:00', '09:00:00', '2020-04-09', 3, NULL),
(20, '17:00:00', '19:00:00', '2020-04-06', 4, NULL),
(21, '21:00:00', '23:00:00', '2020-04-09', 5, NULL),
(22, '19:00:00', '20:00:00', '2020-04-09', 6, NULL),
(23, '11:00:00', '13:00:00', '2020-04-09', 7, NULL),
(24, '09:00:00', '12:00:00', '2020-04-10', 8, NULL),
(25, '13:00:00', '14:30:00', '2020-04-10', 9, NULL),
(26, '08:00:00', '09:00:00', '2020-04-10', 10, NULL),
(27, '09:00:00', '11:00:00', '2020-04-13', 11, NULL),
(28, '19:00:00', '19:30:00', '2020-04-10', 12, NULL),
(29, '13:00:00', '13:30:00', '2020-04-16', 13, NULL),
(30, '13:00:00', '13:30:00', '2020-04-30', 14, NULL),
(31, '17:00:00', '17:30:00', '2020-04-14', 15, NULL),
(32, '09:00:00', '11:00:00', '2020-04-11', 16, NULL),
(33, '09:00:00', '11:00:00', '2020-05-12', 17, NULL),
(34, '20:00:00', '21:00:00', '2020-04-10', 18, NULL),
(35, '10:00:00', '13:00:00', '2020-05-13', 19, NULL),
(36, '15:00:00', '16:00:00', '2020-05-06', 20, NULL),
(37, '15:00:00', '16:00:00', '2020-05-08', 21, NULL),
(38, '12:00:00', '13:00:00', '2020-05-25', 22, NULL),
(39, '15:00:00', '17:00:00', '2020-05-05', 23, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `emission`
--

DROP TABLE IF EXISTS `emission`;
CREATE TABLE IF NOT EXISTS `emission` (
  `emission_id` int(50) NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) NOT NULL,
  `resume` text,
  `fichier` varchar(1024) DEFAULT NULL,
  `animateur` int(50) DEFAULT NULL,
  `programme_id` int(50) DEFAULT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`emission_id`),
  KEY `animateur` (`animateur`),
  KEY `programme_id` (`programme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `emission`
--

INSERT INTO `emission` (`emission_id`, `titre`, `resume`, `fichier`, `animateur`, `programme_id`, `deleted_at`) VALUES
(1, 'Emission 23 avec JUL', 'Votre émission habituelle avec notre invité exceptionnel qui présentera son nouveau son. JUL sera avec nous !!', NULL, 19, 3, NULL),
(2, 'EMISSION SPECIAL CORONAVIRUS', 'Les derniers chiffres liés au COVID-19 dans notre émission spéciale Coronavirus', NULL, 16, 2, NULL),
(3, 'Les objets en cuisine', 'Découvrez les objets indispensables pour votre cuisine !', NULL, 19, 1, NULL),
(4, 'Tuto Makeup 23', 'Les conseils makeup de votre youtubeuse préférée Julianetta ! #23', 'emissions/tuto_makeup_23/out.ogg', 17, 4, NULL),
(5, 'Rap contenders JUL vs GRADUR', 'Emission spéciale avec nos invités JUL et GRADUR qui s\'affronteront dans un rap contenders !', NULL, 18, 3, NULL),
(6, 'Ete 2020', 'Découvrez les futurs sons de l\'été 2020 qui approche à grand pas', NULL, 17, 5, NULL),
(7, 'Futur Hits', 'Retrouvez les futurs hits de cette année 2020 avec de nouveaux artistes et de nouveaux sons', NULL, 17, 5, NULL),
(8, 'Throwback 90', 'Retournez dans les années 90 avec tous les grand hits de cette décennie.', NULL, 16, 5, NULL),
(9, 'Chic avec 50 euros', 'DanaeMakeup sera avec nous dans cette émission consacrée à un maquillage chic pour un budget de seulement 50euros !!', NULL, 18, 4, NULL),
(10, 'Gadget pas cher', 'Venez écouter les conseils et astuces de notre invité Martin pour dénicher des gadgets à moindre coûts', NULL, 16, 1, NULL),
(11, 'Les objets inutiles', 'Avec tous les objets qui facilitent votre vie quotidienne, on vous présentera des objets qui n\'ont aucune utilité pour vous et n\'importe qui.', NULL, 18, 1, NULL),
(12, 'Infos France 15', 'Toute l\'information sur l\'actualité en France dans votre émission journalière #15', NULL, 17, 2, NULL),
(13, 'Infos etranger 8', 'Tout l\'actualité sur les pays étrangers dans votre émission hebdomadaire #8', NULL, 17, 2, NULL),
(14, 'Info France 16', 'Toute l\'actualité sur le territoire français dans votre émission journalière #16', NULL, 16, 2, NULL),
(15, 'Info Covid-19 2', 'Retrouvez l\'actualité sur le Covid-19 dans cette émission spéciale #2', NULL, 18, 2, NULL),
(16, 'Les nouveaux produits 14', 'Retrouvez les nouveaux produits de votre programme consacré spécialement aux objets du quotidien #14', NULL, 19, 1, NULL),
(17, 'Produits de votre chambre 2', 'La deuxième émission consacrée aux objets à installer dans votre chambre à coucher pour votre confort #2', NULL, 19, 1, NULL),
(18, 'Concert live de Gims', 'Gims a l\'honneur de venir nous interpréter son nouvel album en live sur Net\'Radio  ', NULL, 16, 3, NULL),
(19, 'Rappeurs des années 90', 'Dans cette émission nous feront un point sur les rappeurs des années 90. Que sont-ils devenu ? Un invité surprise sera présent avec nous', NULL, 16, 3, NULL),
(20, 'Conseils lifestyle 3', 'Une nouvelle émission consacrée aux conseils beauté en compagnie de Christina Cordula #3', NULL, 19, 4, NULL),
(21, 'Qui est la meilleure', 'Emission spéciale qui opposera TaylorMakeup à Ashlee, nos deux collègues de Net\'Radio sur qui sera la meilleur influenceuse beauté', NULL, 19, 4, NULL),
(22, 'Concentration Maximum', 'Ecoutez toutes nos musiques spécialement choisies pour votre concentration', NULL, 16, 5, NULL),
(23, 'Sport intense', 'Une sélection de musiques spécialement adaptées pour une bonne séance de sport intense', NULL, 18, 5, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

DROP TABLE IF EXISTS `favoris`;
CREATE TABLE IF NOT EXISTS `favoris` (
  `favoris_id` int(50) NOT NULL AUTO_INCREMENT,
  `programme_id` int(50) NOT NULL,
  `utilisateur_id` int(50) NOT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`favoris_id`),
  KEY `programme_id` (`programme_id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `programme`
--

DROP TABLE IF EXISTS `programme`;
CREATE TABLE IF NOT EXISTS `programme` (
  `programme_id` int(50) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `description` text,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`programme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `programme`
--

INSERT INTO `programme` (`programme_id`, `nom`, `description`, `deleted_at`) VALUES
(1, 'Radio Matin', 'Découvrez les tout nouveaux produits de votre émission matinale qui vous faciliteront votre vie quotidienne.', NULL),
(2, 'Le JT', 'Retrouvez toute l\'information sur l\'actualité média du monde entier.', NULL),
(3, 'Rap FR', 'L\'actualité de vos rappeurs FR dans votre radio préférée.', NULL),
(4, 'Conseils beauté', 'Retrouvez tous les conseils beauté de notre amie Julianetta', NULL),
(5, 'Musiques', 'Écoutez toutes les musiques du moments sur Net\'Radio', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `utilisateur_id` int(50) NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(50) NOT NULL,
  `password` varchar(500) NOT NULL,
  `email` varchar(100) NOT NULL,
  `droit` int(5) DEFAULT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`utilisateur_id`, `identifiant`, `password`, `email`, `droit`, `deleted_at`) VALUES
(3, 'Kendji', '$2y$10$JKKRjI6gT8G7PQpJqXa9zOOHGDPVRSiBSZ6oL23NrM45vjYG8o2N.', 'az@az.fr', 0, NULL),
(5, 'allan', '$2y$10$B6qgFUOYPd2Fsn2SQfUzrOGJ8tFXHuq..cAHl.gSbtg0WEB/Zn8zq', 'a@i.fr', 2, NULL),
(6, '', '$2y$10$RH88POZqlakKXa1K2MGZEO6NAEB0hrUF7zbSOxVRpCfJppJIfaABG', '', 1, '2020-02-12'),
(7, 'Théo', '$2y$10$vS6I5YlXQfVCi2wILA7nnOOAtWekOJY0J2o8X1XL58NZx9w0l4/q2', 'theo@coco.fr', 2, NULL),
(8, 'azerty', '$2y$10$yuS0xfW2Fb7.SESS1puPIuQNHgQGmBDeE3nR0txSAH5ZOoJ2gL4LC', 'zz@ii.fr', 0, NULL),
(9, 'allan01', '$2y$10$z1/9lwgg5bJehERkMymmL.lULezjpbd20ps.Dqq0AguuqCI.aohB.', 'allanizzi@lol.fr', 0, NULL),
(16, 'allanizzi', '$2y$10$WtBlekpAYj2.gb1cmwqRm./0ANmTjhadApEYzKgPPGKzEOG2DQZK.', 'allanizzi@hotmail.fr', 0, NULL),
(17, 'legrand54', '$2y$10$Xjj5lYQer3nhyKegd4enG.aGymFURGCDLGI5MNlNCkrqWGM7Op0Gi', 'theolegrand@gmail.fr', 0, NULL),
(18, 'Thomas', '$2y$10$naT636zFq3Uc5uxNZWSJkul0xYBWooHkjXZYHWfXLH/S6fj4QE3Ve', 'thomas@gmail.fr', 0, NULL),
(19, 'Thebas', '$2y$10$HKcP1iqz6FGQqpoDHc34w.BxW88s795MxCyMgD/OHsNTMvtZ5guRC', 'theohelf@hotmail.com', 0, NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `creneau`
--
ALTER TABLE `creneau`
  ADD CONSTRAINT `creneau_ibfk_1` FOREIGN KEY (`emission_id`) REFERENCES `emission` (`emission_id`);

--
-- Contraintes pour la table `emission`
--
ALTER TABLE `emission`
  ADD CONSTRAINT `emission_ibfk_1` FOREIGN KEY (`animateur`) REFERENCES `utilisateur` (`utilisateur_id`),
  ADD CONSTRAINT `emission_ibfk_2` FOREIGN KEY (`programme_id`) REFERENCES `programme` (`programme_id`);

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`programme_id`) REFERENCES `programme` (`programme_id`),
  ADD CONSTRAINT `favoris_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

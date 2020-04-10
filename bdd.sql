# noinspection SqlNoDataSourceInspectionForFile

CREATE DATABASE IF NOT EXISTS `netradio` CHARACTER SET utf8 COLLATE utf8_general_ci;
use `netradio`;

CREATE TABLE IF NOT EXISTS utilisateur(
  utilisateur_id int(50) AUTO_INCREMENT,
  identifiant varchar(50) NOT NULL,
  password varchar(500) NOT NULL,
  email varchar(100) NOT NULL,
  droit int(5),
  deleted_at date,
  PRIMARY KEY (utilisateur_id)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS programme(
  programme_id int(50) AUTO_INCREMENT,
  nom varchar(50) NOT NULL,
  description text,
  deleted_at date,
  PRIMARY KEY (programme_id)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS emission(
  emission_id int(50) AUTO_INCREMENT,
  titre varchar(50) NOT NULL,
  resume text,
  fichier varchar(1024),
  animateur int(50),
  programme_id int(50),
  deleted_at date,
  FOREIGN KEY (animateur) references utilisateur(utilisateur_id),
  FOREIGN KEY (programme_id) references programme(programme_id),
  PRIMARY KEY (emission_id)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS favoris(
    favoris_id int(50) AUTO_INCREMENT,
    programme_id int(50) NOT NULL,
    utilisateur_id int(50) NOT NULL,
    deleted_at date,
    FOREIGN KEY (programme_id) references programme(programme_id),
    FOREIGN KEY (utilisateur_id) references utilisateur(utilisateur_id),
    PRIMARY KEY (favoris_id)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS creneau(
  creneau_id int(50) AUTO_INCREMENT,
  heure_debut time NOT NULL,
  heure_fin time NOT NULL,
  date_creneau date NOT NULL,
  emission_id int(50),
  deleted_at date,
  foreign key (emission_id) references emission(emission_id),
  PRIMARY KEY (creneau_id)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

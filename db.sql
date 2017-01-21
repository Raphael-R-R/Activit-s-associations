DROP DATABASE IF EXISTS GestionActivites;
CREATE DATABASE GestionActivites;
USE GestionActivites;

DROP TABLE IF EXISTS activite;
DROP TABLE IF EXISTS lieu;

CREATE TABLE lieu
(
	id_lieu INT(10) NOT NULL AUTO_INCREMENT,
	nom_lieu VARCHAR(20) DEFAULT NULL,
	PRIMARY KEY(id_lieu)
)Engine=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE activite
(
	idActivite INT(10) NOT NULL AUTO_INCREMENT,
	nomActivite VARCHAR(50) DEFAULT NULL,
	dateCreation DATE NOT NULL,
	coutInscription FLOAT(5, 2) NOT NULL,
	type VARCHAR(20) DEFAULT NULL,
	id_lieu INT(10) NOT NULL,
	PRIMARY KEY(idActivite),
	CONSTRAINT fk_id_lieu FOREIGN KEY(id_lieu) REFERENCES lieu(id_lieu)
)Engine=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO lieu VALUES (1, 'Gymnase IUT');
INSERT INTO lieu VALUES (2, 'Gymnase du phare');
INSERT INTO lieu VALUES (3, 'Stade d athlétisme');
INSERT INTO lieu VALUES (4, 'Site du Malsaucy');
INSERT INTO lieu VALUES (5, 'Piscine');

INSERT INTO activite VALUES (1, 'pêche' , '2015-02-02','100.00', 'loisirs','2');
INSERT INTO activite VALUES (2, 'tir à l arc' , '2013-02-02','135', 'sport','4');
INSERT INTO activite VALUES (3, 'handball' , '2013-02-02','100.00', 'sport','1');
INSERT INTO activite VALUES (4, 'Atelier musique' , '2000-02-02', '200.00', 'loisirs','3');
INSERT INTO activite VALUES (5, 'cuisine' , '2015-02-02','250.00', 'loisirs', '4');
INSERT INTO activite VALUES (6, 'Football' , '2015-02-02','105.00', 'sport','3');
INSERT INTO activite VALUES (7, 'Musculation' , '2015-02-02', '100.00', 'sport','1');
INSERT INTO activite VALUES (8, 'Natation', '2014-10-25','180', 'sport', '5');
INSERT INTO activite VALUES (9, 'Tennis', '2015-02-01', '200', 'sport', '1');
INSERT INTO activite VALUES (10, 'Ping Pong', '2000-10-20', '130', 'sport','1');
INSERT INTO activite VALUES (11, 'Escalade', '2014-11-15', '100', 'sport', '2');
INSERT INTO activite VALUES (12, 'zumba', '2014-10-15','140', 'sport','1' );
INSERT INTO activite VALUES (13, 'Basketball', '2013-06-01','120', 'sport', '1');
INSERT INTO activite VALUES (14, 'Volley', '2013-05-08','125', 'sport', '1');

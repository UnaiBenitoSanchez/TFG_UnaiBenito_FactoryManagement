-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema GestionDeFabricas
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `GestionDeFabricas`;

-- -----------------------------------------------------
-- Schema GestionDeFabricas
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `GestionDeFabricas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `GestionDeFabricas`;

-- -----------------------------------------------------
-- Table `GestionDeFabricas`.`factory_boss`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GestionDeFabricas`.`factory_boss`;

CREATE TABLE IF NOT EXISTS `GestionDeFabricas`.`factory_boss` (
  `factory_id_factory` INT NOT NULL,
  `boss_id_boss_factory` INT NOT NULL,
  PRIMARY KEY (`factory_id_factory`, `boss_id_boss_factory`),
  CONSTRAINT `fk_factory_boss_factory`
    FOREIGN KEY (`factory_id_factory`)
    REFERENCES `GestionDeFabricas`.`factory` (`id_factory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_factory_boss_boss`
    FOREIGN KEY (`boss_id_boss_factory`)
    REFERENCES `GestionDeFabricas`.`boss` (`id_boss_factory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `GestionDeFabricas`.`boss`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GestionDeFabricas`.`boss`;

CREATE TABLE IF NOT EXISTS `GestionDeFabricas`.`boss` (
  `id_boss_factory` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(225) NOT NULL,
  `email` VARCHAR(225) NOT NULL,
  `password` VARCHAR(10000) NOT NULL, 
  PRIMARY KEY (`id_boss_factory`),
  INDEX `idx_boss_email` (`email` ASC) VISIBLE
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `GestionDeFabricas`.`factory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GestionDeFabricas`.`factory`;

CREATE TABLE IF NOT EXISTS `GestionDeFabricas`.`factory` (
  `id_factory` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `street_address` VARCHAR(255) NOT NULL,
  `city` VARCHAR(255) NOT NULL,
  `state` VARCHAR(255) NOT NULL,
  `country` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_factory`)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `GestionDeFabricas`.`category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GestionDeFabricas`.`category`;

CREATE TABLE IF NOT EXISTS `GestionDeFabricas`.`category` (
  `id_category` INT NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(255) NOT NULL,
  `category_description` TEXT NOT NULL,
  PRIMARY KEY (`id_category`)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `GestionDeFabricas`.`product`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GestionDeFabricas`.`product`;

CREATE TABLE IF NOT EXISTS `GestionDeFabricas`.`product` (
  `id_product` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `category_id_category` INT NOT NULL,
  PRIMARY KEY (`id_product`),
  INDEX `fk_product_category_idx` (`category_id_category` ASC) VISIBLE,
  CONSTRAINT `fk_product_category`
    FOREIGN KEY (`category_id_category`)
    REFERENCES `GestionDeFabricas`.`category` (`id_category`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `GestionDeFabricas`.`inventory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GestionDeFabricas`.`inventory`;

CREATE TABLE IF NOT EXISTS `GestionDeFabricas`.`inventory` (
  `id_inventory` INT NOT NULL AUTO_INCREMENT,
  `available_quantity` INT NOT NULL,
  `update_date` DATE NOT NULL,
  `product_id_product` INT NOT NULL,
  `factory_id_factory` INT NOT NULL,
  PRIMARY KEY (`id_inventory`),
  INDEX `fk_inventory_product1_idx` (`product_id_product` ASC) VISIBLE,
  INDEX `fk_inventory_factory1_idx` (`factory_id_factory` ASC) VISIBLE,
  CONSTRAINT `fk_inventory_product1`
    FOREIGN KEY (`product_id_product`)
    REFERENCES `GestionDeFabricas`.`product` (`id_product`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inventory_factory1`
    FOREIGN KEY (`factory_id_factory`)
    REFERENCES `GestionDeFabricas`.`factory` (`id_factory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `GestionDeFabricas`.`inventory_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GestionDeFabricas`.`inventory_history`;

CREATE TABLE IF NOT EXISTS `GestionDeFabricas`.`inventory_history` (
  `id_history` INT NOT NULL AUTO_INCREMENT,
  `product_id_product` INT NOT NULL,
  `change_quantity` INT NOT NULL,
  `change_type` VARCHAR(50) NOT NULL,
  `change_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_history`),
  INDEX `fk_inventory_history_product1_idx` (`product_id_product` ASC) VISIBLE,
  CONSTRAINT `fk_inventory_history_product1`
    FOREIGN KEY (`product_id_product`)
    REFERENCES `GestionDeFabricas`.`product` (`id_product`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `GestionDeFabricas`.`employee`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GestionDeFabricas`.`employee`;

CREATE TABLE IF NOT EXISTS `GestionDeFabricas`.`employee` (
  `id_employee` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(10000) NOT NULL,
  `role` ENUM('worker', 'manager', 'admin') NOT NULL DEFAULT 'worker',
  PRIMARY KEY (`id_employee`)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- -----------------------------------------------------
-- Table `GestionDeFabricas`.`factory_employee`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GestionDeFabricas`.`factory_employee`;

CREATE TABLE IF NOT EXISTS `GestionDeFabricas`.`factory_employee` (
  `factory_id_factory` INT NOT NULL,
  `employee_id_employee` INT NOT NULL,
  PRIMARY KEY (`factory_id_factory`, `employee_id_employee`),
  CONSTRAINT `fk_factory_employee_factory`
    FOREIGN KEY (`factory_id_factory`)
    REFERENCES `GestionDeFabricas`.`factory` (`id_factory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_factory_employee_employee`
    FOREIGN KEY (`employee_id_employee`)
    REFERENCES `GestionDeFabricas`.`employee` (`id_employee`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- Inserts-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------

INSERT INTO category VALUES ('1','Entertainment','Made to let children play with it');

-- Mattel
INSERT INTO boss VALUES ('1','Harold Matson','h4r0ld@gmail.com','$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa');
INSERT INTO factory VALUES ('1', 'Mattel', '333 Continental Blvd', 'El Segundo', 'California', 'USA');
INSERT INTO factory_boss VALUES ('1','1');
INSERT INTO product VALUES('1','Barbie Signature Look Gold Disco - Barbie The Movie','This collectible Barbie® doll sparkles in a sequined disco jumpsuit inspired by her character\'s look in the Barbie™ movie. Her dazzling ensemble is complete with a big disco-inspired mane, metallic jewelry and gold heels.','74.99','img/mattel1.jpg','1');
INSERT INTO inventory VALUES('1','1000','2023-12-22','1','1');
INSERT INTO product VALUES('2','Barbie The Movie Fashion Pack','This Barbie® collectible clothing set recreates three iconic outfits from the Barbie™ movie. Each set comes with shoes and accessories so fans can dress their dolls just like Barbie, the character in the movie.','74.99','img/mattel2.jpg','1');
INSERT INTO inventory VALUES('2','2000','2023-12-22','2','1');
INSERT INTO product VALUES('3','Barbie Signature Ken Perfect Day - Barbie The Movie','Inspired by Ken\'s character in the Barbie™ movie, this Ken® doll is wearing a matching striped beach outfit. With his surfboard, he\'s having the time of his life in Barbie Land! This Ken® doll is a tribute to the Barbie™ movie, making it the perfect gift for fans and collectors.','49.99','img/mattel3.jpg','1');
INSERT INTO inventory VALUES('3','1500','2023-12-22','3','1');
INSERT INTO product VALUES('4','Barbie Cutie Reveal Serie Phantasy Unicorn','Open the box and you\'ll see inside a soft plush unicorn and four surprise bags. Remove the rainbow unicorn costume and you\'ll find a Barbie doll with long hair and sparkly details. Open the surprise bags and discover sparkly clothes, accessories, a sponge-comb and a mini unicorn.','34.99','img/mattel4.jpg','1');
INSERT INTO inventory VALUES('4','1000','2023-12-22','4','1');
INSERT INTO product VALUES('5','Barbie Cutie Reveal Serie Jungle Friends Tiger','Barbie Cutie Reveal Jungle Series dolls offer the cutest unboxing experience with 10 surprises! Discover a charming Elephant, lovable Tiger, bright Toucan or cheeky Monkey, then remove the plush costume to reveal a posable Barbie doll with long, colorful hair. Which doll will you reveal?','32.99','img/mattel5.jpg','1');
INSERT INTO inventory VALUES('5','2000','2023-12-22','5','1');
INSERT INTO product VALUES('6','Disney Frozen Queen Anna & Elsa Snow Queen','Set of two classic dolls, Queen Anna and Snow Queen Elsa. Finely detailed features; Elsa snow queen costume includes satin dress with shimmering lavender organza cape and sleeves. Queen Anna costume includes layered green satin dress with glitter, lined cape and tiara. Beautifully styled, rooted hair; molded shoes and boots.','54.46','img/mattel6.jpg','1');
INSERT INTO inventory VALUES('6','1500','2023-12-22','6','1');

-- Lego
INSERT INTO boss VALUES ('2','Ole Kirk Christiansen','0l3@gmail.com','$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa');
INSERT INTO factory VALUES ('2', 'Lego', '2400 Boulevard Nexxus ADN', 'Ciénega de Flores', 'Nuevo León', 'México');
INSERT INTO factory_boss VALUES ('2','2');
INSERT INTO product VALUES('7','Millennium Falcon','Packed with authentic details! Highly realistic detailing makes this an impressive display item. Relive the Star Wars™ saga. Includes two crews, so you can recreate both classic and new movies.','849.99','img/Millennium_Falcon.jpg','1');
INSERT INTO inventory VALUES('7','1200','2024-01-03','7','2');
INSERT INTO product VALUES('8','Orient Express Train','A paragon of French luxury and an engineering marvel for railroad enthusiasts around the world, the Orient-Express has been stirring imaginations for over 140 years. This new LEGO® Ideas set features interpretations of the train\'s most perfected details along with 8 new character minifigures - it\'s your ticket to endless play and display possibilities!','299.99','img/lego2.jpg','1');
INSERT INTO inventory VALUES('8','2300','2024-01-03','8','2');
INSERT INTO product VALUES('9','Avengers Tower','Recreate the colossal style and grand scale of the Avengers universe\'s most iconic building with the 5201-piece Avengers Tower set. Standing approximately 90 cm tall, this monumental set is more than just a spectacular display piece. It includes a stellar cast of 31 figures that allows you to recreate the battles of the Infinity saga in a multitude of different ways.','499.00','img/lego3.jpg','1');
INSERT INTO inventory VALUES('9','1100','2024-01-03','9','2');
INSERT INTO product VALUES('10','Cherry Blossoms','As well as being a celebration gift for kids, the brick-built blossoms make a great gift for grown-ups, who will be delighted to receive these unique flowers onValentine’s Day or Mother’s Day. Once complete, the set makes a beautiful piece of floral decor that will add a touch of spring joy to any space. It can also be combined with other LEGO flowers (sold separately) to create a vibrant bouquet.','14.99','img/lego4.jpg','1');
INSERT INTO inventory VALUES('10','1200','2024-01-03','10','2');
INSERT INTO product VALUES('11','Disney Ariel Mini Castle','Fans of Disney Princess buildable toys and The Little Mermaid movie aged 12 and up will enjoy endless imaginative role play with this mini model of Ariel’s enchanting palace. Mini Disney Ariel’s Castle (40708) is covered in golden details, incorporates various underwater features and includes an Ariel mini-doll figure. This portable buildable playset is part of the Mini Disney range of companion construction toys, sold separately.','39.99','img/lego5.jpg','1');
INSERT INTO inventory VALUES('11','2300','2024-01-03','11','2');
INSERT INTO product VALUES('12','Natural History Museum','Discover the first-ever museum to join the Modular Buildings collection. Home to an array of brick-built exhibits it features dual skylights that allow light to permeate the building’s 2 levels, illuminating the towering brachiosaurus skeleton and collection of treasures within.','299.99','img/lego6.jpg','1');
INSERT INTO inventory VALUES('12','1100','2024-01-03','12','2');

-- Nerf
INSERT INTO boss VALUES ('3','Reyn Guyer','r3yn@gmail.com','$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa');
INSERT INTO factory VALUES ('3', 'Nerf', '1027 New State Hwy', 'East Longmeadow', 'Massachusetts', 'USA');
INSERT INTO factory_boss VALUES ('3','3');
INSERT INTO product VALUES('13','SMG-Zesty de Nerf Fortnite','The Nerf Fortnite SMG-Zesty Launcher SMG-Zesty is inspired by Fortnite\'s Zesty wrapper, which mirrors the look of the popular video game\'s wrapper. The launcher has a removable barrel and a detachable stock so you can customize it in different ways. Attach them, detach them.... It\'s up to you! Remove them when you\'re in close combat for a more compact launcher.','37.99','img/nerf1.jpg','1');
INSERT INTO inventory VALUES('13','2100','2024-01-03','13','3');
INSERT INTO product VALUES('14','Nerf Ultra Select','Nerf - Ultra Select, a Nerf Ultra launcher that combines range, speed and precision. It is automatic and has 2 compartments in its integrated clip. It can throw 20 Nerf Ultra and Ultra AccuStrike darts for greater accuracy....','32.99','img/nerf2.jpg','1');
INSERT INTO inventory VALUES('14','1800','2024-01-03','14','3');
INSERT INTO product VALUES('15','Nerf DinoSquad Stegosmash','Hold your ground as a dart-throwing defender with the Nerf DinoSquad Stego-Smash launcher! It features an incredible dinosaur design that replicates the appearance of a Stegosaurus dinosaur. Throw yourself into "dinotastic" fun with this one dart launcher; load a dart through the front of the barrel, pull the lever back to prime the launcher and pull the trigger to launch the dart at the target.','19.99','img/nerf3.jpg','1');
INSERT INTO inventory VALUES('15','2050','2024-01-03','15','3');
INSERT INTO product VALUES('16','Nerf DinoSquad Rex-Rampage','A motorized launcher that roars like the king of the dinosaurs!!! It has a 10 dart holder in the butt so you have projectiles for easy reloading. Includes 20 Nerf foam darts for quick firing.','20.98','img/nerf4.jpg','1');
INSERT INTO inventory VALUES('16','2100','2024-01-03','16','3');
INSERT INTO product VALUES('17','Nerf Alpha Strike Slinger SD-1','The Slinger SD-1 Aiming Set includes 1 launcher, 2 target pieces and 4 Nerf Elite darts. The launcher launches 1 dart at a time and is easy to use. Insert 1 dart into the barrel, pull the handle to set it up and pull the trigger to launch 1 dart. Practice your aiming skills with the 2 target pieces that you can put together to form 1 whole target.','7.99','img/nerf5.jpg','1');
INSERT INTO inventory VALUES('17','1800','2024-01-03','17','3');
INSERT INTO product VALUES('18','Nerf Alpha Strike - Mission Set','This 31-piece Nerf Alpha Strike Mission Set includes 4 launchers, 25 darts and targets to practice your aim and play Nerf games. Perfect for gifts, parties or play anytime! Includes 2 Stinger SD-1 launchers, 1 Cobra RC-6 launcher, 1 Tiger DB-2 launcher and 2 target pieces that can be snapped together to form 1 whole target.','24.99','img/nerf6.jpg','1');
INSERT INTO inventory VALUES('18','2050','2024-01-03','18','3');

-- Playmobil
INSERT INTO boss VALUES ('4','Hans Beck','h4ns@gmail.com','$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa');
INSERT INTO factory VALUES ('4', 'Playmobil', 'Brandstätterstraße 2-10', 'Zirndorf', 'Bavaria', 'Germany');
INSERT INTO factory_boss VALUES ('4','4');
INSERT INTO product VALUES('19','Playmobil City Life Family','Modern family figure set with accessories for playing everyday life scenes.', '29.99','img/playmobil1.jpg','1');
INSERT INTO inventory VALUES('19','1500','2024-01-03','19','4');
INSERT INTO product VALUES('20','Playmobil Pirate Ship','Detailed pirate ship with pirate figures, cannons and treasure for exciting adventures.', '89.99','img/playmobil2.jpg','1');
INSERT INTO inventory VALUES('20','800','2024-01-03','20','4');
INSERT INTO product VALUES('21','Playmobil Police Car','Police vehicle with lights and sound, includes officer figures for rescue missions.', '34.99','img/playmobil3.jpg','1');
INSERT INTO inventory VALUES('21','1200','2024-01-03','21','4');
INSERT INTO product VALUES('22','Playmobil Hospital','Complete hospital with emergency room, medical equipment and doctor/patient figures.', '119.99','img/playmobil4.jpg','1');
INSERT INTO inventory VALUES('22','600','2024-01-03','22','4');
INSERT INTO product VALUES('23','Playmobil Farm with Animals','Traditional farm with animals, tractor and farmer figures for rural play.', '59.99','img/playmobil5.jpg','1');
INSERT INTO inventory VALUES('23','900','2024-01-03','23','4');
INSERT INTO product VALUES('24','Playmobil Medieval Castle','Impressive castle with knights, dragon and accessories for epic battles.', '149.99','img/playmobil6.jpg','1');
INSERT INTO inventory VALUES('24','500','2024-01-03','24','4');
INSERT INTO product VALUES('25','Playmobil Passenger Plane','Commercial airplane with passenger and crew figures, perfect for travel play.', '79.99','img/playmobil7.jpg','1');
INSERT INTO inventory VALUES('25','700','2024-01-03','25','4');
INSERT INTO product VALUES('26','Playmobil Space Station','Space base with astronauts, spacecraft and accessories for space exploration.', '99.99','img/playmobil8.jpg','1');
INSERT INTO inventory VALUES('26','650','2024-01-03','26','4');

-- Insert employees for Mattel (Factory 1)
INSERT INTO employee VALUES 
('1', 'Alice Johnson', 'alice.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('2', 'Bob Smith', 'bob.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('3', 'Charlie Brown', 'charlie.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('4', 'David Wilson', 'david.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('5', 'Emma Davis', 'emma.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('6', 'Frank Miller', 'frank.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('7', 'Grace Lee', 'grace.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('8', 'Henry Adams', 'henry.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('9', 'Ivy White', 'ivy.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('10', 'Jack Harris', 'jack.mattel@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker');

-- Assign employees to Mattel (Factory 1)
INSERT INTO factory_employee VALUES 
('1', '1'), ('1', '2'), ('1', '3'), ('1', '4'), ('1', '5'), 
('1', '6'), ('1', '7'), ('1', '8'), ('1', '9'), ('1', '10');

-- Insert employees for Lego (Factory 2)
INSERT INTO employee VALUES 
('11', 'Ava Nelson', 'ava.lego@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('12', 'Ben Foster', 'ben.lego@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('13', 'Chloe Ramirez', 'chloe.lego@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('14', 'Daniel Reed', 'daniel.lego@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('15', 'Ella Perry', 'ella.lego@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker');

-- Assign employees to Lego (Factory 2)
INSERT INTO factory_employee VALUES 
('2', '11'), ('2', '12'), ('2', '13'), ('2', '14'), ('2', '15');

-- Insert employees for Nerf (Factory 3)
INSERT INTO employee VALUES 
('16', 'Aaron Phillips', 'aaron.nerf@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('17', 'Bella Scott', 'bella.nerf@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('18', 'Carter Adams', 'carter.nerf@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('19', 'Diana Clark', 'diana.nerf@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('20', 'Ethan Rodriguez', 'ethan.nerf@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker');

-- Assign employees to Nerf (Factory 3)
INSERT INTO factory_employee VALUES 
('3', '16'), ('3', '17'), ('3', '18'), ('3', '19'), ('3', '20');

-- Insert employees for Playmobil (Factory 4)
INSERT INTO employee VALUES 
('21', 'Klaus Müller', 'klaus.playmobil@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('22', 'Anna Schmidt', 'anna.playmobil@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('23', 'Thomas Fischer', 'thomas.playmobil@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('24', 'Sabine Weber', 'sabine.playmobil@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('25', 'Michael Wagner', 'michael.playmobil@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker');

-- Assign employees to Playmobil (Factory 4)
INSERT INTO factory_employee VALUES 
('4', '21'), ('4', '22'), ('4', '23'), ('4', '24'), ('4', '25');

-- Scripts----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- Mattel------------------------------------------------------------------------------------------------------------
-- Event to delete from Barbie Signature Look Gold Disco - Barbie The Movie
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_Barbie_Signature_Look
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN

  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Look Gold Disco - Barbie The Movie'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Look Gold Disco - Barbie The Movie');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Look Gold Disco - Barbie The Movie'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Look Gold Disco - Barbie The Movie')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Barbie Signature Look Gold Disco - Barbie The Movie
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_Barbie_Signature_Look
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Look Gold Disco - Barbie The Movie'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Look Gold Disco - Barbie The Movie');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Look Gold Disco - Barbie The Movie'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Look Gold Disco - Barbie The Movie')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Barbie The Movie Fashion Pack
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_Barbie_The_Movie_Fashion_Pack
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie The Movie Fashion Pack'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie The Movie Fashion Pack');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie The Movie Fashion Pack'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie The Movie Fashion Pack')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Barbie The Movie Fashion Pack
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_Barbie_The_Movie_Fashion_Pack
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie The Movie Fashion Pack'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie The Movie Fashion Pack');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie The Movie Fashion Pack'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie The Movie Fashion Pack')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Barbie Signature Ken Perfect Day - Barbie The Movie
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_Barbie_Signature_Ken
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Ken Perfect Day - Barbie The Movie'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Ken Perfect Day - Barbie The Movie');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Ken Perfect Day - Barbie The Movie'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Ken Perfect Day - Barbie The Movie')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Barbie Signature Ken Perfect Day - Barbie The Movie
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_Barbie_Signature_Ken
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Ken Perfect Day - Barbie The Movie'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Ken Perfect Day - Barbie The Movie');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Ken Perfect Day - Barbie The Movie'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Signature Ken Perfect Day - Barbie The Movie')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Barbie Cutie Reveal Serie Phantasy Unicorn 
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_unicorn 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Phantasy Unicorn'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Phantasy Unicorn');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Phantasy Unicorn'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Phantasy Unicorn')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Barbie Cutie Reveal Serie Phantasy Unicorn 
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_unicorn
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Phantasy Unicorn'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Phantasy Unicorn');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Phantasy Unicorn'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Phantasy Unicorn')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Barbie Cutie Reveal Serie Jungle Friends Tiger 
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_tiger 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Jungle Friends Tiger'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Jungle Friends Tiger');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Jungle Friends Tiger'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Jungle Friends Tiger')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to CatNap 
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_tiger
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Jungle Friends Tiger'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Jungle Friends Tiger');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Jungle Friends Tiger'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Barbie Cutie Reveal Serie Jungle Friends Tiger')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Disney Frozen Queen Anna & Elsa Snow Queen 
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_elsaAna
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Frozen Queen Anna & Elsa Snow Queen'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Frozen Queen Anna & Elsa Snow Queen');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Frozen Queen Anna & Elsa Snow Queen'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Frozen Queen Anna & Elsa Snow Queen')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Disney Frozen Queen Anna & Elsa Snow Queen 
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_elsaAna 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Frozen Queen Anna & Elsa Snow Queen'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Frozen Queen Anna & Elsa Snow Queen');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Frozen Queen Anna & Elsa Snow Queen'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Frozen Queen Anna & Elsa Snow Queen')), 'Add');
END;
//
DELIMITER ;

-- Lego--------------------------------------------------------------------------------------------------------------
-- Event to delete from Millennium Falcon
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_Millennium_Falcon
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN

  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Millennium Falcon'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Millennium Falcon');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Millennium Falcon'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Millennium Falcon')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Millennium Falcon
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_Millennium_Falcon
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Millennium Falcon'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Millennium Falcon');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Millennium Falcon'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Millennium Falcon')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Orient Express Train
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_Orient_Express_Train
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Orient Express Train'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Orient Express Train');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Orient Express Train'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Orient Express Train')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Orient Express Train
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_Orient_Express_Train
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Orient Express Train'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Orient Express Train');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Orient Express Train'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Orient Express Train')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Avengers Tower
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_Avengers_Tower
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Avengers Tower'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Avengers Tower');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Avengers Tower'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Avengers Tower')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Avengers Tower
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_Avengers_Tower
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Avengers Tower'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Avengers Tower');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Avengers Tower'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Avengers Tower')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Cherry Blossoms 
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_cherry
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Cherry Blossoms'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Cherry Blossoms');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Cherry Blossoms'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Cherry Blossoms')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Cherry Blossoms 
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_cherry 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Cherry Blossoms'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Cherry Blossoms');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Cherry Blossoms'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Cherry Blossoms')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Disney Ariel Mini Castle 
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_ariel 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Ariel Mini Castle'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Ariel Mini Castle');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Ariel Mini Castle'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Ariel Mini Castle')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Disney Ariel Mini Castle 
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_ariel 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Ariel Mini Castle'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Ariel Mini Castle');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Ariel Mini Castle'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Disney Ariel Mini Castle')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Natural History Museum 
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_museum
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Natural History Museum'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Natural History Museum');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Natural History Museum'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Natural History Museum')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Natural History Museum 
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_museum 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Natural History Museum'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Natural History Museum');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Natural History Museum'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Natural History Museum')), 'Add');
END;
//
DELIMITER ;

-- Nerf--------------------------------------------------------------------------------------------------------------
-- Event to delete from SMG-Zesty de Nerf Fortnite
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_SMG_Zesty_de_Nerf_Fortnite
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN

  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'SMG-Zesty de Nerf Fortnite'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'SMG-Zesty de Nerf Fortnite');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'SMG-Zesty de Nerf Fortnite'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'SMG-Zesty de Nerf Fortnite')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to SMG-Zesty de Nerf Fortnite
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_SMG_Zesty_de_Nerf_Fortnite
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'SMG-Zesty de Nerf Fortnite'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'SMG-Zesty de Nerf Fortnite');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'SMG-Zesty de Nerf Fortnite'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'SMG-Zesty de Nerf Fortnite')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Nerf Ultra Select
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_Nerf_Ultra_Select
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Ultra Select'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Ultra Select');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Ultra Select'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Ultra Select')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Nerf Ultra Select
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_Nerf_Ultra_Select
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Ultra Select'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Ultra Select');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Ultra Select'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Ultra Select')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Nerf DinoSquad Stegosmash
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_Nerf_DinoSquad_Stegosmash
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Stegosmash'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Stegosmash');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Stegosmash'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Stegosmash')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Nerf DinoSquad Stegosmash
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_Nerf_DinoSquad_Stegosmash
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Stegosmash'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Stegosmash');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Stegosmash'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Stegosmash')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Nerf DinoSquad Rex-Rampage 
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_rex 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Rex-Rampage'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Rex-Rampage');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Rex-Rampage'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Rex-Rampage')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Nerf DinoSquad Rex-Rampage 
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_rex 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Rex-Rampage'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Rex-Rampage');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Rex-Rampage'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf DinoSquad Rex-Rampage')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Nerf Alpha Strike Slinger SD-1 
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_slinger
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike Slinger SD-1'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike Slinger SD-1');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike Slinger SD-1'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike Slinger SD-1')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Nerf Alpha Strike Slinger SD-1 
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_slinger 
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike Slinger SD-1'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike Slinger SD-1');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike Slinger SD-1'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike Slinger SD-1')), 'Add');
END;
//
DELIMITER ;

-- Event to delete from Nerf Alpha Strike - Mission Set 
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_mission
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike - Mission Set'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 100, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike - Mission Set');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike - Mission Set'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike - Mission Set')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Nerf Alpha Strike - Mission Set 
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_mission
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike - Mission Set'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 100
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike - Mission Set');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike - Mission Set'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Nerf Alpha Strike - Mission Set')), 'Add');
END;
//
DELIMITER ;
-- Playmobil Events ------------------------------------------------------------------------------------------------------

-- Event to subtract from Playmobil City Life Family
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_city_life
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil City Life Family'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 50, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil City Life Family');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil City Life Family'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil City Life Family')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Playmobil City Life Family
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_city_life
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil City Life Family'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 50
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil City Life Family');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil City Life Family'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil City Life Family')), 'Add');
END;
//
DELIMITER ;

-- Event to subtract from Playmobil Pirate Ship
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_pirate_ship
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Pirate Ship'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 20, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Pirate Ship');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Pirate Ship'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Pirate Ship')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Playmobil Pirate Ship
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_pirate_ship
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Pirate Ship'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 20
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Pirate Ship');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Pirate Ship'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Pirate Ship')), 'Add');
END;
//
DELIMITER ;

-- Event to subtract from Playmobil Police Car
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_police_car
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Police Car'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 40, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Police Car');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Police Car'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Police Car')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Playmobil Police Car
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_police_car
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Police Car'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 40
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Police Car');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Police Car'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Police Car')), 'Add');
END;
//
DELIMITER ;

-- Event to subtract from Playmobil Hospital
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_hospital
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Hospital'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 15, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Hospital');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Hospital'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Hospital')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Playmobil Hospital
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_hospital
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Hospital'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 15
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Hospital');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Hospital'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Hospital')), 'Add');
END;
//
DELIMITER ;

-- Event to subtract from Playmobil Farm with Animals
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_farm
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Farm with Animals'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 30, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Farm with Animals');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Farm with Animals'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Farm with Animals')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Playmobil Farm with Animals
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_farm
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Farm with Animals'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 30
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Farm with Animals');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Farm with Animals'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Farm with Animals')), 'Add');
END;
//
DELIMITER ;

-- Event to subtract from Playmobil Medieval Castle
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_castle
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Medieval Castle'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 10, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Medieval Castle');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Medieval Castle'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Medieval Castle')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Playmobil Medieval Castle
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_castle
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Medieval Castle'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 10
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Medieval Castle');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Medieval Castle'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Medieval Castle')), 'Add');
END;
//
DELIMITER ;

-- Event to subtract from Playmobil Passenger Plane
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_plane
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Passenger Plane'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 25, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Passenger Plane');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Passenger Plane'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Passenger Plane')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Playmobil Passenger Plane
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_plane
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Passenger Plane'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 25
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Passenger Plane');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Passenger Plane'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Passenger Plane')), 'Add');
END;
//
DELIMITER ;

-- Event to subtract from Playmobil Space Station
DELIMITER //
CREATE EVENT IF NOT EXISTS subtract_quantity_event_space_station
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Space Station'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = GREATEST(available_quantity - 20, 0)
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Space Station');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Space Station'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Space Station')), 'Subtract');
END;
//
DELIMITER ;

-- Event to add to Playmobil Space Station
DELIMITER //
CREATE EVENT IF NOT EXISTS add_quantity_event_space_station
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Space Station'));

  UPDATE GestionDeFabricas.inventory
  SET available_quantity = available_quantity + 20
  WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Space Station');

  INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
  VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Space Station'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = 'Playmobil Space Station')), 'Add');
END;
//
DELIMITER ;

-- -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
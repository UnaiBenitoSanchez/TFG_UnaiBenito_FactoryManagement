SET search_path TO "GestionDeFabricas";

-- Inserts-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------

INSERT INTO category (category_name, category_description) VALUES ('Entertainment', 'Made to let children play with it');

-- Mattel
INSERT INTO boss VALUES ('1','Harold Matson','h4r0ld@gmail.com','$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa');
INSERT INTO factory VALUES ('1', 'Mattel', '333 Continental Blvd', 'El Segundo', 'California', 'USA');
INSERT INTO factory_boss (factory_id_factory,boss_id_boss_factory) VALUES ('1','1');
INSERT INTO product VALUES('1','Barbie Signature Look Gold Disco - Barbie The Movie','This collectible Barbie® doll sparkles in a sequined disco jumpsuit inspired by her character look in the Barbie™ movie. Her dazzling ensemble is complete with a big disco-inspired mane, metallic jewelry and gold heels.','74.99','img/mattel1.jpg','1');
INSERT INTO inventory VALUES('1','1000','2023-12-22','1','1');
INSERT INTO product VALUES('2','Barbie The Movie Fashion Pack','This Barbie® collectible clothing set recreates three iconic outfits from the Barbie™ movie. Each set comes with shoes and accessories so fans can dress their dolls just like Barbie, the character in the movie.','74.99','img/mattel2.jpg','1');
INSERT INTO inventory VALUES('2','2000','2023-12-22','2','1');
INSERT INTO product VALUES('3','Barbie Signature Ken Perfect Day - Barbie The Movie','Inspired by Ken character in the Barbie™ movie, this Ken® doll is wearing a matching striped beach outfit. With his surfboard, he having the time of his life in Barbie Land! This Ken® doll is a tribute to the Barbie™ movie, making it the perfect gift for fans and collectors.','49.99','img/mattel3.jpg','1');
INSERT INTO inventory VALUES('3','1500','2023-12-22','3','1');
INSERT INTO product VALUES('4','Barbie Cutie Reveal Serie Phantasy Unicorn','Open the box and youll see inside a soft plush unicorn and four surprise bags. Remove the rainbow unicorn costume and youll find a Barbie doll with long hair and sparkly details. Open the surprise bags and discover sparkly clothes, accessories, a sponge-comb and a mini unicorn.','34.99','img/mattel4.jpg','1');
INSERT INTO inventory VALUES('4','1000','2023-12-22','4','1');
INSERT INTO product VALUES('5','Barbie Cutie Reveal Serie Jungle Friends Tiger','Barbie Cutie Reveal Jungle Series dolls offer the cutest unboxing experience with 10 surprises! Discover a charming Elephant, lovable Tiger, bright Toucan or cheeky Monkey, then remove the plush costume to reveal a posable Barbie doll with long, colorful hair. Which doll will you reveal?','32.99','img/mattel5.jpg','1');
INSERT INTO inventory VALUES('5','2000','2023-12-22','5','1');
INSERT INTO product VALUES('6','Disney Frozen Queen Anna & Elsa Snow Queen','Set of two classic dolls, Queen Anna and Snow Queen Elsa. Finely detailed features; Elsa snow queen costume includes satin dress with shimmering lavender organza cape and sleeves. Queen Anna costume includes layered green satin dress with glitter, lined cape and tiara. Beautifully styled, rooted hair; molded shoes and boots.','54.46','img/mattel6.jpg','1');
INSERT INTO inventory VALUES('6','1500','2023-12-22','6','1');

-- Lego
INSERT INTO boss VALUES ('2','Ole Kirk Christiansen','0l3@gmail.com','$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa');
INSERT INTO factory VALUES ('2', 'Lego', '2400 Boulevard Nexxus ADN', 'Ciénega de Flores', 'Nuevo León', 'México');
INSERT INTO factory_boss (factory_id_factory,boss_id_boss_factory) VALUES ('2','2');
INSERT INTO product VALUES('7','Millennium Falcon','Packed with authentic details! Highly realistic detailing makes this an impressive display item. Relive the Star Wars™ saga. Includes two crews, so you can recreate both classic and new movies.','849.99','img/Millennium_Falcon.jpg','1');
INSERT INTO inventory VALUES('7','1200','2024-01-03','7','2');
INSERT INTO product VALUES('8','Orient Express Train','A paragon of French luxury and an engineering marvel for railroad enthusiasts around the world, the Orient-Express has been stirring imaginations for over 140 years. This new LEGO® Ideas set features interpretations of the trains most perfected details along with 8 new character minifigures - its your ticket to endless play and display possibilities!','299.99','img/lego2.jpg','1');
INSERT INTO inventory VALUES('8','2300','2024-01-03','8','2');
INSERT INTO product VALUES('9','Avengers Tower','Recreate the colossal style and grand scale of the Avengers universe most iconic building with the 5201-piece Avengers Tower set. Standing approximately 90 cm tall, this monumental set is more than just a spectacular display piece. It includes a stellar cast of 31 figures that allows you to recreate the battles of the Infinity saga in a multitude of different ways.','499.00','img/lego3.jpg','1');
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
INSERT INTO factory_boss (factory_id_factory,boss_id_boss_factory) VALUES ('3','3');
INSERT INTO product VALUES('13','SMG-Zesty de Nerf Fortnite','The Nerf Fortnite SMG-Zesty Launcher SMG-Zesty is inspired by Fortnite Zesty wrapper, which mirrors the look of the popular video game wrapper. The launcher has a removable barrel and a detachable stock so you can customize it in different ways. Attach them, detach them.... Its up to you! Remove them when youre in close combat for a more compact launcher.','37.99','img/nerf1.jpg','1');
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

-- Playtime Co.
INSERT INTO boss VALUES ('4','Elliot Ludwig','3ll1ot@gmail.com','$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa');
INSERT INTO factory VALUES ('4', 'Playtime Co.', '1000 Industrial Ave', 'Los Angeles', 'California', 'USA');
INSERT INTO factory_boss (factory_id_factory,boss_id_boss_factory) VALUES ('4','4');
INSERT INTO product VALUES('19','Bobby BearHug','A kind, caring soul, Bobby BearHug shows compassion for everyone, and for everything. People and places and things, large or small, are all receivers of her love. Each is enriched by this attention and affection, and in turn, so is she.','50.00','img/playtime1.jpg','1');
INSERT INTO inventory VALUES('19','1000','2024-01-03','19','4');
INSERT INTO product VALUES('20','Bubba Bubbaphant','Bubba Bubbaphant is the brains of the critters. Bright and attentive, he keeps his friends steady and always steers them to make smart choices, that way they all might grow up to be bright and brilliant, each in their own right.','30.00','img/playtime2.jpg','1');
INSERT INTO inventory VALUES('20','2000','2024-01-03','20','4');
INSERT INTO product VALUES('21','CraftyCorn','A conscious observer of both color and creativity in the world. CraftyCorn understands the importance of art, and sharing it with others. Crayons, pencils, paint, or words on the page. CraftyCorn can see beauty in anything imagined given shape.','20.00','img/playtime3.jpg','1');
INSERT INTO inventory VALUES('21','3000','2024-01-03','21','4');
INSERT INTO product VALUES('22','DogDay','This is DogDay, the sunny, strong, and determined leader of our critters! Each trusts him to find the bright side in any situation, and to have a friendly word of encouragement should they feel down. Hell always keep his friends going, no matter what.','50.00','img/playtime4.jpg','1');
INSERT INTO inventory VALUES('22','1000','2024-01-03','22','4');
INSERT INTO product VALUES('23','Hoppy Hopscotch','Unafraid to hop where others might sit, Hoppy is the friend everyone needs to maintain their energy and enthusiasm. While sometimes loud or impatient, she will always hop besides her friends, even if it means slowing up once in a while to keep their pace.','30.00','img/playtime5.jpg','1');
INSERT INTO inventory VALUES('23','2000','2024-01-03','23','4');
INSERT INTO product VALUES('24','KickinChicken','This is KickinChicken, the cool kid of the crew, and he maintains that sense of cool through anything, even in the most tense of situations. Knock him down, and he will pick himself up, brush himself off, and ask: "Whats next?"','20.00','img/playtime6.jpg','1');
INSERT INTO inventory VALUES('24','3000','2024-01-03','24','4');
INSERT INTO product VALUES('25','PickyPiggy','Whats more important than play and learning? PickyPiggy knows the answer. A playful body and keen mind are fueled by whats put into them, which is why she encourages her friends to eat a well-balanced diet. Secretly, PB&Js are her favorite food.','20.00','img/playtime7.jpg','1');
INSERT INTO inventory VALUES('25','3000','2024-01-03','25','4');
INSERT INTO product VALUES('26','CatNap','CatNap is a calming presence for the critters and ensures he and his friends always have the right amount of sleep to jumpstart the mornings play! End of the day, theres nothing CatNap enjoys more than watching his friends sleep soundly.','20.00','img/playtime8.jpg','1');
INSERT INTO inventory VALUES('26','3000','2024-01-03','26','4');

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

-- Insert employees for Playtime Co. (Factory 4)
INSERT INTO employee VALUES 
('21', 'Alex Carter', 'alex.playtime@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('22', 'Brooke Adams', 'brooke.playtime@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('23', 'Charlie Lopez', 'charlie.playtime@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('24', 'Daisy Young', 'daisy.playtime@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker'),
('25', 'Elliot Brooks', 'elliot.playtime@email.com', '$2y$10$LvUBjigljVKC1YyIwUwa1OI5lhHEnSGgXGc5NdmDRlhCftWHmPgOa', 'worker');

-- Assign employees to Playtime Co. (Factory 4)
INSERT INTO factory_employee VALUES 
('4', '21'), ('4', '22'), ('4', '23'), ('4', '24'), ('4', '25');
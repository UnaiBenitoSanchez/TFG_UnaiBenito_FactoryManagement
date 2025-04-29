-- Eliminar el esquema si existe y crearlo nuevamente
DROP SCHEMA IF EXISTS "GestionDeFabricas" CASCADE;
CREATE SCHEMA "GestionDeFabricas";

-- Configurar la búsqueda de rutas
SET search_path TO "GestionDeFabricas", public;

-- Tabla boss
CREATE TABLE "GestionDeFabricas"."boss" (
  "id_boss_factory" SERIAL PRIMARY KEY,
  "name" VARCHAR(225) NOT NULL,
  "email" VARCHAR(225) NOT NULL,
  "password" VARCHAR(1000) NOT NULL
);

-- Crear índice en el email
CREATE INDEX "idx_boss_email" ON "GestionDeFabricas"."boss" ("email");

-- Tabla factory
CREATE TABLE "GestionDeFabricas"."factory" (
  "id_factory" SERIAL PRIMARY KEY,
  "name" VARCHAR(255) NOT NULL,
  "street_address" VARCHAR(255) NOT NULL,
  "city" VARCHAR(255) NOT NULL,
  "state" VARCHAR(255) NOT NULL,
  "country" VARCHAR(255) NOT NULL
);

-- Tabla de relación factory_boss (se crea después de factory y boss)
CREATE TABLE "GestionDeFabricas"."factory_boss" (
  "factory_id_factory" INT NOT NULL,
  "boss_id_boss_factory" INT NOT NULL,
  PRIMARY KEY ("factory_id_factory", "boss_id_boss_factory"),
  CONSTRAINT "fk_factory_boss_factory"
    FOREIGN KEY ("factory_id_factory")
    REFERENCES "GestionDeFabricas"."factory" ("id_factory")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT "fk_factory_boss_boss"
    FOREIGN KEY ("boss_id_boss_factory")
    REFERENCES "GestionDeFabricas"."boss" ("id_boss_factory")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

-- Tabla category
CREATE TABLE "GestionDeFabricas"."category" (
  "id_category" SERIAL PRIMARY KEY,
  "category_name" VARCHAR(255) NOT NULL,
  "category_description" TEXT NOT NULL
);

-- Tabla product
CREATE TABLE "GestionDeFabricas"."product" (
  "id_product" SERIAL PRIMARY KEY,
  "name" VARCHAR(255) NOT NULL,
  "description" TEXT NOT NULL,
  "price" DECIMAL(10,2) NOT NULL,
  "image" VARCHAR(255) NOT NULL,
  "category_id_category" INT NOT NULL,
  CONSTRAINT "fk_product_category"
    FOREIGN KEY ("category_id_category")
    REFERENCES "GestionDeFabricas"."category" ("id_category")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

-- Crear índice para la relación con category
CREATE INDEX "fk_product_category_idx" ON "GestionDeFabricas"."product" ("category_id_category");

-- Tabla inventory
CREATE TABLE "GestionDeFabricas"."inventory" (
  "id_inventory" SERIAL PRIMARY KEY,
  "available_quantity" INT NOT NULL,
  "update_date" DATE NOT NULL,
  "product_id_product" INT NOT NULL,
  "factory_id_factory" INT NOT NULL,
  CONSTRAINT "fk_inventory_product1"
    FOREIGN KEY ("product_id_product")
    REFERENCES "GestionDeFabricas"."product" ("id_product")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT "fk_inventory_factory1"
    FOREIGN KEY ("factory_id_factory")
    REFERENCES "GestionDeFabricas"."factory" ("id_factory")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

-- Crear índices para inventory
CREATE INDEX "fk_inventory_product1_idx" ON "GestionDeFabricas"."inventory" ("product_id_product");
CREATE INDEX "fk_inventory_factory1_idx" ON "GestionDeFabricas"."inventory" ("factory_id_factory");

-- Tabla inventory_history
CREATE TABLE "GestionDeFabricas"."inventory_history" (
  "id_history" SERIAL PRIMARY KEY,
  "product_id_product" INT NOT NULL,
  "change_quantity" INT NOT NULL,
  "change_type" VARCHAR(50) NOT NULL,
  "change_timestamp" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT "fk_inventory_history_product1"
    FOREIGN KEY ("product_id_product")
    REFERENCES "GestionDeFabricas"."product" ("id_product")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

-- Crear índice para inventory_history
CREATE INDEX "fk_inventory_history_product1_idx" ON "GestionDeFabricas"."inventory_history" ("product_id_product");

-- Tabla employee
CREATE TABLE "GestionDeFabricas"."employee" (
  "id_employee" SERIAL PRIMARY KEY,
  "name" VARCHAR(255) NOT NULL,
  "email" VARCHAR(255) NOT NULL UNIQUE,
  "password" VARCHAR(1000) NOT NULL,
  "role" VARCHAR(10) NOT NULL DEFAULT 'worker' CHECK ("role" IN ('worker', 'manager', 'admin'))
);

-- Tabla factory_employee
CREATE TABLE "GestionDeFabricas"."factory_employee" (
  "factory_id_factory" INT NOT NULL,
  "employee_id_employee" INT NOT NULL,
  PRIMARY KEY ("factory_id_factory", "employee_id_employee"),
  CONSTRAINT "fk_factory_employee_factory"
    FOREIGN KEY ("factory_id_factory")
    REFERENCES "GestionDeFabricas"."factory" ("id_factory")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT "fk_factory_employee_employee"
    FOREIGN KEY ("employee_id_employee")
    REFERENCES "GestionDeFabricas"."employee" ("id_employee")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);
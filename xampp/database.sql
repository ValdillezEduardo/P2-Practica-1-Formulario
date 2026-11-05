-- Crea la base de datos si no existe
CREATE DATABASE IF NOT EXISTS nike_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nike_db;

-- Tabla de usuarios registrados
CREATE TABLE IF NOT EXISTS usuarios (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- identificador único
  nombre          VARCHAR(60)  NOT NULL,
  apellido        VARCHAR(60)  NOT NULL,
  email           VARCHAR(120) NOT NULL UNIQUE,            -- no se puede repetir
  password_hash   VARCHAR(255) NOT NULL,                   -- contraseña cifrada
  fecha_registro  TIMESTAMP DEFAULT CURRENT_TIMESTAMP      -- se llena sola
) ENGINE=InnoDB;

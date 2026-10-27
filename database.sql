CREATE DATABASE IF NOT EXISTS tienda CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE tienda;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  correo VARCHAR(120) NOT NULL UNIQUE,
  clave VARCHAR(255) NOT NULL,
  creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS marcas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT NOT NULL,
  nombre VARCHAR(60) NOT NULL,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE IF NOT EXISTS pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  marca_id INT NOT NULL,
  producto VARCHAR(120) NOT NULL,
  cantidad INT NOT NULL,
  direccion VARCHAR(250) NOT NULL,
  notas VARCHAR(500),
  creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (marca_id) REFERENCES marcas(id)
);

INSERT INTO categorias (nombre) VALUES ('Ropa'),('Cosméticos'),('Calzado'),('Celulares'),('Accesorios');

INSERT INTO marcas (categoria_id, nombre) VALUES
(1,'Zara'),(1,'H&M'),(1,'Levi''s'),(1,'Tommy Hilfiger'),
(2,'MAC'),(2,'Maybelline'),(2,'L''Oréal'),(2,'Dior'),
(3,'Nike'),(3,'Adidas'),(3,'Puma'),(3,'Converse'),
(4,'Apple'),(4,'Samsung'),(4,'Xiaomi'),(4,'Motorola'),
(5,'Ray-Ban'),(5,'Casio'),(5,'JBL');

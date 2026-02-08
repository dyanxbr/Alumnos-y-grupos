
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_escolar;
USE sistema_escolar;

-- Tabla de carreras
CREATE TABLE carreras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
);

-- Tabla de turnos
CREATE TABLE turnos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de grados
CREATE TABLE grados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de grupos
CREATE TABLE grupos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    carrera_id INT NOT NULL,
    turno_id INT NOT NULL,
    grado_id INT NOT NULL,
    nombre_grupo VARCHAR(50) NOT NULL,
    FOREIGN KEY (carrera_id) REFERENCES carreras(id),
    FOREIGN KEY (turno_id) REFERENCES turnos(id),
    FOREIGN KEY (grado_id) REFERENCES grados(id)
);

-- Tabla de alumnos
CREATE TABLE alumnos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    grupo_id INT NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupo_id) REFERENCES grupos(id)
);

-- Insertar datos de ejemplo
INSERT INTO carreras (nombre) VALUES 
('Ingeniería en Sistemas'),
('Administración de Empresas'),
('Contaduría Pública'),
('Derecho'),
('Psicología');

INSERT INTO turnos (nombre) VALUES 
('Matutino'),
('Vespertino'),
('Nocturno'),
('Mixto');

INSERT INTO grados (nombre) VALUES 
('Primero'),
('Segundo'),
('Tercero'),
('Cuarto'),
('Quinto');

-- Insertar grupos de ejemplo
INSERT INTO grupos (carrera_id, turno_id, grado_id, nombre_grupo) VALUES 
(1, 1, 1, 'IS-1A'),
(1, 1, 2, 'IS-2A'),
(2, 2, 1, 'AE-1B'),
(3, 1, 3, 'CP-3A');
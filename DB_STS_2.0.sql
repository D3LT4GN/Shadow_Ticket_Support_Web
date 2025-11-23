/*
 * SCRIPT CONSOLIDADO DE BASE DE DATOS
 * Versión: 1.3
 *
 * CAMBIOS (v1.3 - 16/11/2025):
 * 1. Simplificados cat_estados a 3 (Abierto, En Progreso, Resuelto).
 * 2. Simplificados cat_prioridades a 3 (Baja, Media, Alta).
 * 3. Simplificados cat_tallas a 3 (S, M, L).
 *
 * CAMBIOS (v1.2):
 * 1. Eliminada la tabla 'Roles'.
 * 2. La tabla 'agentes' se renombra y fusiona en 'Administrador'.
 * 3. La tabla 'Administrador' usa los datos de 'Insert ShadowTicket.sql'.
 * 4. Eliminada la columna 'Id_rol' de la nueva tabla 'Administrador'.
 * 5. La tabla 'tickets' ahora se vincula con 'Administrador'.
 */

-- ==== Tablas de Catálogos ====

CREATE TABLE cat_estados(
    id_estado SERIAL PRIMARY KEY,
    nombre_estado VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE cat_prioridades(
    id_prioridad SERIAL PRIMARY KEY,
    nombre_prioridad VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE cat_tallas(
    id_talla SERIAL PRIMARY KEY,
    talla VARCHAR(10) UNIQUE NOT NULL,
    descripcion VARCHAR(255),
    horas_estimadas INT
);

-- ==== Tabla de Clientes ====

CREATE TABLE clientes(
    id_cliente SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    segundo_nombre VARCHAR(100),
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    pass VARCHAR(50) NOT NULL, -- En producción, esto debería ser un hash
    telefono NUMERIC NOT NULL,
    empresa VARCHAR(150),
    fecha_registro TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ==== Tabla 'Administrador' (Reemplaza agentes/Usuario) ====

CREATE TABLE Administrador(
    id_administrador SERIAL PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Apellido VARCHAR(100) NOT NULL,
    Correo VARCHAR(255) UNIQUE NOT NULL,
    Pass VARCHAR(255) NOT NULL -- En producción, esto debería ser un hash
);


-- ==== Tabla 'tickets' (Modificada v1.2) ====

CREATE TABLE tickets(
    id_ticket SERIAL PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_administrador_asignado INT, 
    id_estado INT NOT NULL,
    id_prioridad INT NOT NULL,
    id_talla INT,
    titulo VARCHAR(250) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP,
    fecha_actualizada TIMESTAMP,
    fecha_cierre TIMESTAMP WITH TIME ZONE,
    
    CONSTRAINT fk_cliente FOREIGN KEY(id_cliente) REFERENCES clientes(id_cliente),
    CONSTRAINT fk_administrador FOREIGN KEY(id_administrador_asignado) REFERENCES Administrador(id_administrador) ON DELETE SET NULL,
    CONSTRAINT fk_estado FOREIGN KEY(id_estado) REFERENCES cat_estados(id_estado),
    CONSTRAINT fk_prioridad FOREIGN KEY(id_prioridad) REFERENCES cat_prioridades(id_prioridad),
    CONSTRAINT fk_talla FOREIGN KEY(id_talla) REFERENCES cat_tallas(id_talla)
);

-- ==== Tabla 'comentarios' (Modificada v1.2) ====

CREATE TABLE comentarios(
    id_comentario SERIAL PRIMARY KEY,
    id_ticket INT NOT NULL,
    id_administrador INT NOT NULL,
    comentario TEXT NOT NULL,
    fecha_creacion TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    
    CONSTRAINT fk_ticket FOREIGN KEY(id_ticket) REFERENCES tickets(id_ticket) ON DELETE CASCADE,
    CONSTRAINT fk_comentario_admin FOREIGN KEY(id_administrador) REFERENCES Administrador(id_administrador)
);


-- =============================================
-- ==== INSERCIÓN DE DATOS (CONSOLIDADO v1.3) ====
-- =============================================

-- Estados de un ticket (SIMPLIFICADO)
INSERT INTO cat_estados (nombre_estado) VALUES
('Abierto'),
('En Progreso'),
('Resuelto');

-- Niveles de prioridad (SIMPLIFICADO)
INSERT INTO cat_prioridades (nombre_prioridad) VALUES
('Baja'),
('Media'),
('Alta');

-- Tallas del sistema T-shirt Sizing (SIMPLIFICADO)
INSERT INTO cat_tallas (talla, descripcion) VALUES
('S', 'Tarea pequeña'),
('M', 'Tarea mediana'),
('L', 'Tarea grande');


-- ==== INSERTS DE 'Administrador' (v1.2) ====

INSERT INTO Administrador (Nombre, Apellido, Correo, Pass) VALUES
('Ivan', 'Jimenez', 'ivan.jimenez@shadow.com', '3310'),
('Yecsan', 'Villegas', 'yecsan.villegas@shadow.com', '3315'),
('Johann', 'Gonzalez', 'johann.gonzalez@shadow.com', '3320'),
('Kevin', 'Mejia', 'kevin.mejia@shadow.com', '3325'),
('Fredy', 'Ortiz', 'fredy.ortiz@shadow.com', '3330');
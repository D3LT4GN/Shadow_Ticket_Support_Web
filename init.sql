-- =============================================
-- 1. TABLAS DE CATÁLOGOS (Lookups)
-- =============================================

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
    horas_estimadas INT -- Dato informativo para el cálculo de T-shirt Sizing
);

-- =============================================
-- 2. TABLAS DE USUARIOS (Roles separados)
-- =============================================

-- Tabla para los clientes que levantan tickets
CREATE TABLE clientes(
    id_cliente SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    segundo_nombre VARCHAR(100),
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    pass VARCHAR(50) NOT NULL, -- En producción real usar hash (bcrypt)
    telefono NUMERIC NOT NULL,
    empresa VARCHAR(150),
    fecha_registro TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Tabla para el Staff de Soporte (Administradores)
CREATE TABLE Administrador(
    id_administrador SERIAL PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Apellido VARCHAR(100) NOT NULL,
    Correo VARCHAR(255) UNIQUE NOT NULL,
    Pass VARCHAR(255) NOT NULL -- En producción real usar hash
);

-- =============================================
-- 3. TABLA PRINCIPAL: TICKETS
-- =============================================

CREATE TABLE tickets(
    id_ticket SERIAL PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_administrador_asignado INT, 
    id_estado INT NOT NULL,
    id_prioridad INT NOT NULL,
    id_talla INT,
    titulo VARCHAR(250) NOT NULL,
    descripcion TEXT,
    -- Nueva columna para notas internas del admin (Agregada en Fase Admin)
    notas_admin TEXT,
    fecha_creacion TIMESTAMP DEFAULT NOW(),
    fecha_actualizada TIMESTAMP,
    fecha_cierre TIMESTAMP WITH TIME ZONE,
    -- Relaciones (Foreign Keys)
    CONSTRAINT fk_cliente FOREIGN KEY(id_cliente) REFERENCES clientes(id_cliente),
    CONSTRAINT fk_administrador FOREIGN KEY(id_administrador_asignado) REFERENCES Administrador(id_administrador) ON DELETE SET NULL,
    CONSTRAINT fk_estado FOREIGN KEY(id_estado) REFERENCES cat_estados(id_estado),
    CONSTRAINT fk_prioridad FOREIGN KEY(id_prioridad) REFERENCES cat_prioridades(id_prioridad),
    CONSTRAINT fk_talla FOREIGN KEY(id_talla) REFERENCES cat_tallas(id_talla)
);

-- =============================================
-- 4. TABLA DE CHAT: COMENTARIOS
-- =============================================

CREATE TABLE comentarios(
    id_comentario SERIAL PRIMARY KEY,
    id_ticket INT NOT NULL,
    -- Modificación: Ahora pueden ser NULL para saber quién escribió
    id_administrador INT, 
    id_cliente INT,       
    comentario TEXT NOT NULL,
    fecha_creacion TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    CONSTRAINT fk_ticket FOREIGN KEY(id_ticket) REFERENCES tickets(id_ticket) ON DELETE CASCADE,
    CONSTRAINT fk_comentario_admin FOREIGN KEY(id_administrador) REFERENCES Administrador(id_administrador),
    CONSTRAINT fk_comentario_cliente FOREIGN KEY(id_cliente) REFERENCES clientes(id_cliente)
);

-- =============================================
-- 5. INSERCIÓN DE DATOS SEMILLA (Seed Data)
-- =============================================

-- Estados del Flujo de Trabajo
INSERT INTO cat_estados (nombre_estado) VALUES
('Abierto'),      -- ID 1: Ticket recién creado
('En Progreso'),  -- ID 2: Admin trabajando
('Resuelto');     -- ID 3: Ticket cerrado

-- Niveles de Urgencia
INSERT INTO cat_prioridades (nombre_prioridad) VALUES
('Baja'),   -- ID 1
('Media'),  -- ID 2
('Alta');   -- ID 3

-- Metodología T-shirt Sizing
INSERT INTO cat_tallas (talla, descripcion) VALUES
('S', 'Tarea pequeña'),
('M', 'Tarea mediana'),
('L', 'Tarea grande');

-- Administradores Iniciales (Staff)
INSERT INTO Administrador (Nombre, Apellido, Correo, Pass) VALUES
('Ivan', 'Jimenez', 'ivan.jimenez@shadow.com', '3310'),
('Yecsan', 'Villegas', 'yecsan.villegas@shadow.com', '3315'),
('Johann', 'Gonzalez', 'johann.gonzalez@shadow.com', '3320'),
('Kevin', 'Mejia', 'kevin.mejia@shadow.com', '3325'),
('Fredy', 'Ortiz', 'fredy.ortiz@shadow.com', '3330');

-- Cliente de Prueba (Para demos)
INSERT INTO clientes (nombre, apellido_paterno, apellido_materno, email, pass, telefono, empresa) VALUES
('Usuario', 'Prueba', 'Demo', 'test@shadow.com', '12345', 5551234567, 'Cliente Demo Corp');
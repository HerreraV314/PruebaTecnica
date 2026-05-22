-- Creación de tabla Monedas
CREATE TABLE monedas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

INSERT INTO monedas (nombre) VALUES 
('PESO CHILENO'),
('DÓLAR'),
('EURO');

-- Creación de tabla Bodegas
CREATE TABLE bodegas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

INSERT INTO bodegas (nombre) VALUES 
('Bodega 1'),
('Bodega 2'),
('Bodega 3');

-- Creación de tabla Sucursales
CREATE TABLE sucursales (
    id SERIAL PRIMARY KEY,
    bodega_nombre VARCHAR(100) NOT NULL,
    nombre VARCHAR(100) NOT NULL
);

-- Insertamos sucursales asociadas a los nombres de las bodegas que creaste antes
INSERT INTO sucursales (bodega_nombre, nombre) VALUES 
('Bodega 2', 'Sucursal 2A'),
('Bodega 2', 'Sucursal 2B'),
('Bodega 1', 'Sucursal 1A'),
('Bodega 3', 'Sucursal 3A');

-- Creación de tabla Principal de Productos
CREATE TABLE productos (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(15) UNIQUE NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    bodega VARCHAR(100) NOT NULL,
    sucursal VARCHAR(100) NOT NULL,
    moneda VARCHAR(50) NOT NULL,
    precio NUMERIC(10, 2) NOT NULL,
    descripcion TEXT NOT NULL
);

CREATE TABLE producto_materiales (
    id SERIAL PRIMARY KEY,
    producto_id INT REFERENCES productos(id) ON DELETE CASCADE,
    material VARCHAR(50) NOT NULL
);
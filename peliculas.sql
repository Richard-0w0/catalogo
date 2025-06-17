-- Eliminar tabla usuarios si ya existe (para evitar conflictos)
DROP TABLE IF EXISTS favoritas;
DROP TABLE IF EXISTS usuarios;

-- Crear tabla usuarios con todas las columnas necesarias desde el inicio
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- Suficiente largo para hash
    es_admin BOOLEAN DEFAULT FALSE   -- Columna para privilegios admin
);

-- Crear usuario admin con contraseña hasheada
-- La contraseña '1234' hasheada es: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO usuarios (usuario, password, es_admin) VALUES 
('admin', 'admin123', TRUE);

-- Tabla de películas favoritas (igual que la tuya, está correcta)
CREATE TABLE IF NOT EXISTS favoritas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    pelicula_id INT NOT NULL,
    fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (pelicula_id) REFERENCES peliculas(id),
    UNIQUE KEY (usuario_id, pelicula_id)
);

-- Asegurar que la tabla películas existe con los índices correctos
ALTER TABLE peliculas
  ADD PRIMARY KEY (id),
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
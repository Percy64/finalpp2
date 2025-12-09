-- Migración: Actualizar estructura de tabla mascotas
-- Ejecutar si hay error "Unknown column 'm.estado'" o similar

USE mascotas_db;

-- 1. Agregar columna estado si no existe
ALTER TABLE mascotas 
ADD COLUMN IF NOT EXISTS estado ENUM('normal','perdida','encontrada') DEFAULT 'normal' AFTER foto_url;

-- 2. Agregar columna fecha_estado si no existe
ALTER TABLE mascotas 
ADD COLUMN IF NOT EXISTS fecha_estado DATETIME DEFAULT NULL AFTER estado;

-- 3. Agregar columna descripcion_estado si no existe
ALTER TABLE mascotas 
ADD COLUMN IF NOT EXISTS descripcion_estado TEXT DEFAULT NULL AFTER fecha_estado;

-- 4. Renombrar columna sexo a genero (si existe)
-- Para MariaDB 10.5.2+ o MySQL 8.0.0+
-- ALTER TABLE mascotas RENAME COLUMN sexo TO genero;

-- Para versiones anteriores:
ALTER TABLE mascotas CHANGE COLUMN sexo genero ENUM('macho','hembra') DEFAULT NULL;

-- 5. Migrar datos: marcar mascotas perdidas según columna legacy 'perdido'
UPDATE mascotas SET estado = 'perdida', fecha_estado = NOW() WHERE perdido = 1;

-- 6. Verificar estructura actualizada
DESCRIBE mascotas;

-- 7. Verificar distribución de estados
SELECT COUNT(*) as total, estado FROM mascotas GROUP BY estado;

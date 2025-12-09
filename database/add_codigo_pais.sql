-- Agregar columna codigo_pais a la tabla usuarios
ALTER TABLE `usuarios` ADD COLUMN `codigo_pais` VARCHAR(5) DEFAULT '+54' AFTER `telefono`;

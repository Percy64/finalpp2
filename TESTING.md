# 🧪 Guía de Testing - Sistema MVC

## Acceso al Sistema

URL principal: **http://localhost/finalpp2/**

## ✅ Checklist de Testing

### 1. Página Principal (Home)
- [ ] Acceder a `http://localhost/finalpp2/`
- [ ] Verificar que se muestran las mascotas perdidas
- [ ] Verificar que los CSS cargan correctamente
- [ ] Verificar que las imágenes de mascotas se muestran
- [ ] Verificar que las estadísticas se muestran correctamente
- [ ] Click en "Reportar Mascota" debe redirigir a login (si no estás logueado)

### 2. Registro de Usuario
- [ ] Acceder a `http://localhost/finalpp2/registro`
- [ ] Completar formulario con datos válidos
- [ ] Verificar que se crea el usuario en la BD
- [ ] Verificar redirección a perfil después del registro
- [ ] Probar subir una foto de perfil

### 3. Login
- [ ] Acceder a `http://localhost/finalpp2/login`
- [ ] Probar login con credenciales incorrectas (debe mostrar error)
- [ ] Probar login con credenciales correctas
- [ ] Verificar que se inicia sesión correctamente
- [ ] Verificar redirección a perfil

### 4. Perfil de Usuario
- [ ] Acceder a `http://localhost/finalpp2/perfil`
- [ ] Verificar que se muestra información del usuario
- [ ] Verificar que se listan las mascotas del usuario
- [ ] Click en "Editar perfil" debe llevar a formulario de edición
- [ ] Click en "Agregar" mascota debe llevar a registro de mascota

### 5. Editar Perfil
- [ ] Acceder a `http://localhost/finalpp2/editar-perfil`
- [ ] Cambiar nombre, teléfono, dirección
- [ ] Subir nueva foto de perfil
- [ ] Cambiar contraseña
- [ ] Verificar que los cambios se guardan correctamente

### 6. Recuperar Contraseña
- [ ] Acceder a `http://localhost/finalpp2/recuperar-password`
- [ ] Ingresar email registrado
- [ ] Verificar que se crea token en tabla `password_resets`
- [ ] Copiar URL generada y acceder
- [ ] Cambiar contraseña
- [ ] Verificar que se actualiza en la BD

### 7. Registrar Mascota
- [ ] Acceder a `http://localhost/finalpp2/registrar-mascota`
- [ ] Completar formulario (nombre, especie, raza, edad, color, género)
- [ ] Subir foto de mascota
- [ ] Verificar que se crea en tabla `mascotas`
- [ ] Verificar que se genera código QR
- [ ] Verificar redirección a perfil de mascota

### 8. Perfil de Mascota
- [ ] Acceder a `http://localhost/finalpp2/mascota/1` (usar ID real)
- [ ] Verificar que se muestra información completa
- [ ] Verificar que se muestra foto
- [ ] Verificar que se muestra info del dueño
- [ ] Si eres dueño: verificar botones "Editar" y "Eliminar"
- [ ] Si NO eres dueño: verificar que NO aparecen esos botones

### 9. Editar Mascota
- [ ] Acceder a `http://localhost/finalpp2/mascota/1/editar`
- [ ] Cambiar datos (nombre, edad, descripción)
- [ ] Subir nueva foto
- [ ] Verificar que los cambios se guardan

### 10. Eliminar Mascota
- [ ] Acceder a `http://localhost/finalpp2/mascota/1/eliminar`
- [ ] Verificar mensaje de confirmación
- [ ] Confirmar eliminación
- [ ] Verificar que se elimina de la BD
- [ ] Verificar que se elimina archivo de foto
- [ ] Verificar que se eliminan registros relacionados (historial_medico, codigos_qr)

### 11. Información QR
- [ ] Acceder a `http://localhost/finalpp2/qr/1`
- [ ] Verificar que se muestra info de contacto del dueño
- [ ] Probar click en teléfono (debe abrir marcador)
- [ ] Probar click en email (debe abrir cliente de email)

### 12. Mapa de Búsqueda
- [ ] Acceder a `http://localhost/finalpp2/mapa`
- [ ] Verificar que se muestra interfaz de búsqueda
- [ ] (Google Maps: implementación futura)

### 13. Navegación (Bottom Nav)
- [ ] Verificar que aparece la barra inferior en todas las páginas
- [ ] Click en Home (🏠) → debe ir a `/`
- [ ] Click en Buscar (🔍) → debe ir a `/mapa`
- [ ] Click en Perfil (👤) → debe ir a `/perfil` (o `/login` si no estás logueado)
- [ ] Click en Info (ℹ️) → debe ir a página legal

### 14. Logout
- [ ] Acceder a `http://localhost/finalpp2/logout`
- [ ] Verificar que se destruye la sesión
- [ ] Verificar redirección a home
- [ ] Intentar acceder a `/perfil` → debe redirigir a `/login`

## 🔍 Queries SQL para Verificar

```sql
-- Ver usuarios registrados
SELECT * FROM usuarios;

-- Ver mascotas registradas
SELECT * FROM mascotas;

-- Ver códigos QR generados
SELECT * FROM codigos_qr;

-- Ver tokens de password reset
SELECT * FROM password_resets;

-- Ver mascotas de un usuario específico
SELECT * FROM mascotas WHERE id = 1;
```

## 🐛 Errores Comunes

### Error: "404 - Página no encontrada"
- Verificar que `.htaccess` tiene RewriteBase `/finalpp2/`
- Verificar que mod_rewrite está habilitado en Apache
- Verificar que la ruta en `config/config.php` es `/finalpp2`

### Error: CSS no carga
- Verificar que `BASE_URL` está correctamente definido en `config/config.php`
- Verificar rutas en las vistas: `<?= ASSETS_URL ?>/css/...`

### Error: Imágenes no se muestran
- Verificar permisos de carpeta `assets/images/`
- Verificar que las rutas en BD son relativas: `/assets/images/mascotas/...`
- Verificar que `ROOT_PATH` está correctamente definido

### Error: "Call to undefined method"
- Verificar que el controlador hereda de `Controller`
- Verificar que los modelos se cargan correctamente
- Verificar nombres de métodos en el router

### Error: Base de datos
- Verificar que la BD `mascotas_db` existe
- Verificar credenciales en `config/database.php`
- Verificar que todas las tablas están creadas

## 📊 Métricas de Éxito

- ✅ Todas las rutas funcionan
- ✅ CRUD completo de usuarios funciona
- ✅ CRUD completo de mascotas funciona
- ✅ Autenticación y sesiones funcionan
- ✅ Upload de imágenes funciona
- ✅ Generación de QR funciona
- ✅ Navegación entre páginas funciona
- ✅ CSS y assets cargan correctamente

## 🎯 Próximo Nivel

Una vez que todo funcione:
1. Agregar validaciones AJAX
2. Implementar cambio de estado de mascotas (perdida/encontrada)
3. Integrar Google Maps API
4. Agregar notificaciones por email
5. Implementar búsqueda en tiempo real
6. Agregar paginación en listados
7. Implementar sistema de mensajería entre usuarios

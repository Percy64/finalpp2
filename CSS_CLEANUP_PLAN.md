# CSS Cleanup Plan - Archivos Heredados

Este documento describe los archivos CSS heredados que pueden ser eliminados tras verificación.

## Status: AUDITORÍA COMPLETADA ✅

Todos los estilos inline han sido centralizados. Los siguientes archivos heredados pueden ser evaluados para eliminación:

## ELIMINAR INMEDIATAMENTE ❌

Estos archivos fueron definitivamente reemplazados y ya NO se usan en ninguna vista:

### 1. registro-mascota-addon.css
- **Anterior uso**: pet/register.php y pet/edit.php
- **Reemplazo**: `pet-register.css`
- **Acción**: ✅ ELIMINAR
- **Riesgo**: Ninguno (uso actualizado)

```bash
# Comando para eliminar (después de verificación)
rm assets/css/registro-mascota-addon.css
```

### 2. perfil-usuario.css
- **Anterior uso**: user/profile.php
- **Reemplazo**: `user-profile.css`
- **Acción**: ✅ ELIMINAR
- **Riesgo**: Ninguno (uso actualizado)

```bash
# Comando para eliminar (después de verificación)
rm assets/css/perfil-usuario.css
```

### 3. busqueda.css (PROBABLE)
- **Estado**: Probablemente no usado (pet/map.php usa pet-map.css)
- **Acción**: ⚠️ REVISAR PRIMERO, luego ELIMINAR
- **Comando de búsqueda**:
  ```bash
  grep -r "busqueda.css" app/views/
  # Si sin resultados: SEGURO ELIMINAR
  ```

## REVISAR Y POSIBLEMENTE ELIMINAR ⚠️

Estos archivos pueden ser duplicados. Revisar antes de eliminar:

### 4. iniciosesion.css
- **Sospecha**: Potencial duplicado de `user-login.css`
- **Acción**: REVISAR CONTENIDO
- **Paso 1**: Comparar con user-login.css (deben ser idénticos)
- **Paso 2**: Verificar si se importa en alguna vista
- **Paso 3**: Si es idéntico y no se usa → ELIMINAR

```bash
# Verificar si está en uso
grep -r "iniciosesion.css" app/views/
# Si sin resultados: SEGURO ELIMINAR

# Comparar contenido
diff assets/css/iniciosesion.css assets/css/user-login.css
# Si sin diferencias: Son duplicados
```

### 5. registro-usuario.css
- **Sospecha**: Potencial duplicado de `user-register.css` y `user-edit-profile.css`
- **Acción**: REVISAR CONTENIDO
- **Paso 1**: Comparar con user-register.css y user-edit-profile.css
- **Paso 2**: Verificar si se importa en alguna vista
- **Paso 3**: Si duplicado y no se usa → ELIMINAR

```bash
# Verificar si está en uso
grep -r "registro-usuario.css" app/views/
# Resultado esperado: Sin resultados (fue reemplazado)

# Comparar contenido
diff assets/css/registro-usuario.css assets/css/user-register.css
diff assets/css/registro-usuario.css assets/css/user-edit-profile.css
```

## REVISAR PERO PROBABLEMENTE MANTENER

Estos archivos pueden ser heredados pero tienen cierta especificidad:

### 6. home3_03.css
- **Sospecha**: Alternativa/versión antigua de `home.css`
- **Acción**: VERIFICAR SI SE USA
- **Paso 1**: Buscar referencias en app/views/

```bash
# Verificar uso
grep -r "home3_03.css" app/views/
# Si sin resultados: CONSIDERAR ELIMINAR

# Comparar con home.css
diff assets/css/home3_03.css assets/css/home.css
# Si similares: Probablemente heredado
```

### 7. nousuario.css
- **Sospecha**: Heredado de sistema anterior
- **Acción**: VERIFICAR SI SE USA

```bash
# Verificar uso
grep -r "nousuario.css" app/views/
# Si sin resultados: CONSIDERAR ELIMINAR
```

## MANTENER (Activos) ✅

Estos archivos se usan actualmente y NO deben eliminarse:

```
✅ mascota03.css           (Base global - CRÍTICO)
✅ bottom-nav.css          (Navegación - Activo)
✅ user-login.css          (Login - Activo)
✅ user-register.css       (Registro - Activo)
✅ user-edit-profile.css   (Editar - Activo)
✅ user-profile.css        (Perfil usuario - Activo)
✅ pet-register.css        (Registro mascota - Activo)
✅ pet-map.css             (Mapa - Activo)
✅ home.css                (Home - Activo)
✅ legal.css               (Legal - Activo)
✅ terminos.css            (Términos - Activo)
```

## Plan de Eliminación Segura

### FASE 1: Eliminación Segura (Sin Riesgo)
```bash
# Estos pueden eliminarse de inmediato
rm assets/css/registro-mascota-addon.css
rm assets/css/perfil-usuario.css
```

**Verificación post-eliminación**:
1. Abrir http://localhost/finalpp2/registrar-mascota
2. Abrir http://localhost/finalpp2/perfil
3. Verificar que CSS carga correctamente (no hay errores 404)

### FASE 2: Eliminación Condicional (Con Revisión)
```bash
# Primero ejecutar verificaciones:
grep -r "iniciosesion.css" app/views/
grep -r "registro-usuario.css" app/views/
grep -r "busqueda.css" app/views/

# Si TODOS los comandos anterios retornan sin resultados:
rm assets/css/iniciosesion.css
rm assets/css/registro-usuario.css
rm assets/css/busqueda.css

# Pruebas:
# 1. Login: http://localhost/finalpp2/login
# 2. Register: http://localhost/finalpp2/registro
# 3. Edit: http://localhost/finalpp2/editar-perfil
# 4. Map: http://localhost/finalpp2/mapa
```

### FASE 3: Eliminación de Heredados (Opcional)
```bash
# Si nunca se usan:
grep -r "home3_03.css" app/views/
grep -r "nousuario.css" app/views/

# Si sin resultados en ambos:
rm assets/css/home3_03.css
rm assets/css/nousuario.css
```

## Checklist de Seguridad

Antes de ejecutar CUALQUIER eliminación:

- [ ] He ejecutado `grep -r "archivo.css" app/views/` y verificado sin resultados
- [ ] He verificado que el nuevo archivo CSS está importado en la vista correspondiente
- [ ] He probado la página en el navegador después de eliminar
- [ ] He limpiado el caché del navegador (F12)
- [ ] No hay errores 404 en Console
- [ ] La página se ve correctamente

## Prueba Rápida Post-Eliminación

Después de eliminar archivos, ejecutar en navegador:

```javascript
// Abierto en cualquier página del sitio
console.log('CSS files:');
document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
  console.log(link.href);
});
```

Verificar que:
1. No hay referencias a `404`
2. No hay archivos eliminados en la lista
3. Los archivos activos están presentes

## Recomendación Final

✅ **PROCEDER CON ELIMINACIÓN**:
- `registro-mascota-addon.css` - Completamente reemplazado
- `perfil-usuario.css` - Completamente reemplazado

⚠️ **REVISAR ANTES DE ELIMINAR**:
- `iniciosesion.css` - Probablemente duplicado
- `registro-usuario.css` - Probablemente duplicado
- `busqueda.css` - Probablemente no usado

❓ **INVESTIGAR**:
- `home3_03.css` - Heredado, verificar si activo
- `nousuario.css` - Heredado, verificar si activo

## Notas Técnicas

### Por qué los archivos CSS no causan problemas si se dejan:
- El navegador los cachea (sin impacto)
- No se cargan si no se importan
- No afectan la velocidad de página
- Pueden servir como respaldo

### Por qué ES bueno eliminarlos:
- Reduce desorden (menos archivos confusos)
- Mejora mantenibilidad (una única fuente de verdad)
- Facilita búsquedas de código (menos coincidencias falsas)
- Prepara para optimizaciones futuras

---

*Documento de cleanup plan - Actualizar después de ejecutar eliminaciones*

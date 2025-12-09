# CSS Organization - Pet Alert

## Resumen de Estructura

El proyecto utiliza una arquitectura CSS modular centralizada en `assets/css/` siguiendo el patrón de separación de responsabilidades por página/componente.

## Archivos CSS Principales

### Base & Global
- **mascota03.css** - Estilos globales base heredados, utilizado en todas las páginas
- **bottom-nav.css** - Barra de navegación inferior consistente en toda la aplicación

### Páginas de Autenticación
- **user-login.css** - Página de inicio de sesión (login.php)
  - Gradiente morado (#667eea → #764ba2)
  - Formulario centrado con animación slideUp
  
- **user-register.css** - Página de registro de usuario (register.php)
  - Grid layout 3fr/9fr para país/teléfono
  - Soporta validación con error-message
  
- **user-edit-profile.css** - Edición de perfil de usuario (edit_profile.php)
  - Botones de envío/cancelación lado a lado
  - Responsive en dispositivos móviles

### Perfil de Usuario
- **user-profile.css** - Perfil público del usuario (profile.php)
  - Cards responsivas con grid
  - Avatar circular con bordes
  - Galería de mascotas (280px square aspect-ratio)
  - Fondo lavanda #fdf6ff

### Mascotas
- **pet-register.css** - Registro de mascota (pet/register.php)
  - Gradiente cyan/azul (#7f7fd5 → #91eae4)
  - Field-row grid para disposición de campos
  - Animación rise
  
- **pet-map.css** - Mapa de mascotas perdidas (pet/map.php)
  - Layout Leaflet.js
  - Grid responsivo para listado de mascotas
  - Badge para estado (perdida/normal)

### Legal & Otros
- **terminos.css** - Páginas legales (terminos-condiciones.php, mision_vision.php)
- **busqueda.css** - Búsqueda y filtros (heredado, en uso en map.php)
- **registro-mascota-addon.css** - Complementos del registro (heredado, puede consolidarse con pet-register.css)
- **iniciosesion.css** - Complementos de login (heredado, puede consolidarse con user-login.css)
- **registro-usuario.css** - Complementos de registro (heredado, puede consolidarse con user-register.css)

## Archivos Heredados (Considerados para Consolidación)

### Duplicados/Obsoletos
- **home3_03.css** - Alternativa de home.css (verificar si se usa)
- **nousuario.css** - Complemento de usuario (reemplazado por user-profile.css)
- **perfil-usuario.css** - Antiguo perfil de usuario (reemplazado por user-profile.css)

## Estilos Centralizados (Extrados)

### Archivos Recientemente Extraídos de Inline
✅ **user-login.css** - Extraído de user/login.php `<style>` block
✅ **user-register.css** - Extraído de user/register.php `<style>` block
✅ **user-edit-profile.css** - Extraído de user/edit_profile.php `<style>` block
✅ **pet-register.css** - Extraído de pet/register.php `<style>` block
✅ **pet-map.css** - Extraído de pet/map.php `<style>` block

## Paleta de Colores

### Gradientes Principales
- **Púrpura Usuarios** - #667eea → #764ba2
  - Usado en: login, register, edit_profile, user-profile
  
- **Cyan Mascotas** - #7f7fd5 → #86a8e7 → #91eae4
  - Usado en: pet-register
  
- **Naranja Home** - Gradiente naranja (home.css)
  - Usado en: home/index.php

### Fondos
- **Lavanda Claro** - #fdf6ff (background principal de aplicación)
- **Blanco** - #ffffff (formularios, cards)
- **Gris Claro** - #e0e0e0 (bordes, inputs)

### Acentos
- **Texto Principal** - #333 / #1f2a44
- **Texto Secundario** - #666 / #6b7280
- **Texto Deshabilitado** - #999
- **Error** - #c00 / #b91c1c
- **Éxito** - #009900 / #155724

## Convenciones

### Nomenclatura de Clases
- **Componentes**: `.formulario`, `.mascota-item`, `.btn_enviar`
- **Layout**: `.form-row`, `.field-row`, `.button-group`
- **Estado**: `.error-message`, `.success-message`, `.estado-perdida`, `.estado-normal`
- **Contenedores**: `.mapa-container`, `.registro-mascota`, `.lista-mascotas`

### Responsive
- Mobile-first approach
- Media query principal: `(max-width: 480px)` para móviles
- Media query tablets: `(max-width: 768px)` para maps
- Media query desktop: `(max-width: 560px)` y superiores

### Animaciones
- **slideUp** - Entrada desde abajo (login, register, edit_profile)
- **rise** - Entrada con escala (pet-register)
- **hover effects** - Elevación con transform translateY(-2px) o (-1px)

## Importación en Vistas

### Template Estándar
```html
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/[page-specific].css">
```

### Ejemplo: Login
```html
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/user-login.css">
```

### Ejemplo: Pet Register
```html
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/pet-register.css">
```

## Plan de Consolidación Pendiente

### Corto Plazo (Completado ✅)
- ✅ Extraer todos los `<style>` inline a archivos CSS
- ✅ Crear user-login.css, user-register.css, user-edit-profile.css
- ✅ Crear pet-register.css, pet-map.css
- ✅ Actualizar referencias en vistas

### Mediano Plazo
- [ ] Consolidar mascota03.css con otros base styles
- [ ] Revisar y eliminar duplicados en iniciosesion.css, registro-usuario.css, registro-mascota-addon.css
- [ ] Verificar uso actual de home3_03.css, nousuario.css, perfil-usuario.css
- [ ] Crear utils.css para clases reutilizables (.btn-primary, .card, .grid-responsive)

### Largo Plazo
- [ ] Implementar preprocessor CSS (SCSS/LESS) si escala el proyecto
- [ ] Crear sistema de design tokens para colores
- [ ] Documentar guía de estilos completa

## Verificación de Implementación

### Vistas Sin Inline `<style>`
✅ app/views/user/login.php
✅ app/views/user/register.php
✅ app/views/user/edit_profile.php
✅ app/views/pet/register.php
✅ app/views/pet/map.php

### Vistas Aún No Verificadas
- [ ] app/views/home/index.php
- [ ] app/views/pet/profile.php
- [ ] app/views/pet/edit.php
- [ ] app/views/pet/delete.php
- [ ] app/views/user/profile.php (ya tiene user-profile.css)
- [ ] app/views/legal/mision_vision.php
- [ ] app/views/legal/terminos-condiciones.html

## Notas Técnicas

### Grid Layout
- **Form Row (Usuario)**: `grid-template-columns: 3fr 9fr` para código país/teléfono
- **Field Row (Mascota)**: `grid-template-columns: repeat(auto-fit, minmax(240px, 1fr))`
- **Pet Cards**: `grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))`

### Responsive Behavior
- Formularios: Centralizados con max-width (420px-640px)
- Cards: Grid que adapta cantidad de columnas según pantalla
- Maps: 500px altura en desktop, 350px en mobile

### Performance
- Todos los CSS están en assets/css/ (sin inline styles)
- Carga única de mascota03.css + específico por página
- Archivos comprimibles y cacheables por navegador

## Mantenimiento Futuro

Cuando agregues nuevas páginas:
1. Crea un archivo CSS específico: `assets/css/[feature-name].css`
2. Nunca uses `<style>` inline en vistas
3. Importa `mascota03.css` primero, luego tu CSS específico
4. Usa la paleta de colores existente
5. Sigue convenciones de nomenclatura de clases
6. Prueba responsive (mobile, tablet, desktop)

# CSS Quick Reference - Pet Alert

## Estructura de Carpetas CSS

```
assets/css/
├── mascota03.css              # 🔧 Base global (SIEMPRE importar primero)
├── bottom-nav.css             # 📱 Navegación inferior
├── user-*.css                 # 👤 Vistas de usuario (login, register, edit, profile)
├── pet-*.css                  # 🐾 Vistas de mascota (register, map)
├── home.css                   # 🏠 Página principal
├── legal.css                  # ⚖️ Páginas legales
└── terminos.css               # 📋 Términos y condiciones
```

## Importación Estándar por Vista

### Vistas de Autenticación
```html
<!-- Login -->
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/user-login.css">

<!-- Register -->
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/user-register.css">

<!-- Edit Profile -->
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/user-edit-profile.css">
```

### Vistas de Perfil & Mascotas
```html
<!-- User Profile -->
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/user-profile.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css" />

<!-- Pet Register / Edit -->
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/pet-register.css">

<!-- Pet Map -->
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/pet-map.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css" />
```

## Paleta de Colores

### Gradientes Principales
| Uso | Valor | Archivo |
|-----|-------|---------|
| Usuarios (Login, Register, etc) | `#667eea → #764ba2` | user-*.css |
| Mascotas (Pet Register) | `#7f7fd5 → #86a8e7 → #91eae4` | pet-register.css |
| Home | Ver home.css | home.css |

### Colores Sólidos
| Nombre | Hex | RGB | Uso |
|--------|-----|-----|-----|
| Fondo Primario | #fdf6ff | 253, 246, 255 | Body/Container |
| Blanco | #ffffff | 255, 255, 255 | Cards, Formularios |
| Gris Claro | #e0e0e0 | 224, 224, 224 | Bordes, Inputs |
| Texto Principal | #333333 | 51, 51, 51 | Encabezados |
| Texto Secundario | #666666 | 102, 102, 102 | Párrafos |
| Verde Legal | #16a085 | 22, 160, 133 | Botones legal |
| Rojo Error | #cc0000 | 204, 0, 0 | Mensajes error |
| Verde Éxito | #009900 | 0, 153, 0 | Estado normal |

## Clases Reutilizables

### Componentes
```css
.formulario        /* Contenedor de formularios */
.btn_enviar        /* Botón principal (submit) */
.btn-cancelar      /* Botón secundario */
.error-message     /* Mensaje de error */
.success-message   /* Mensaje de éxito */
```

### Layouts
```css
.form-row          /* Grid: 3fr 9fr (país/teléfono) */
.form-row-col      /* Columna dentro de form-row */
.field-row         /* Grid auto-fit minmax 240px */
.button-group      /* Flex: botones lado a lado */
```

### Estados
```css
.estado-perdida    /* Badge: mascota perdida */
.estado-normal     /* Badge: mascota normal */
.mascota-item      /* Card de mascota en lista */
```

## Responsive Breakpoints

| Dispositivo | Breakpoint | Cambios |
|-------------|-----------|---------|
| Móvil | `max-width: 480px` | Padding reduced, form-row → 1 col |
| Tablet | `max-width: 768px` | Grid column changes |
| Desktop | `> 1024px` | Full layout |

## Animaciones

### slideUp (Formularios)
```css
@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
```

### rise (Pet Register)
```css
@keyframes rise {
  from { opacity: 0; transform: translateY(24px) scale(0.98); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
```

### Hover Effects
- Formularios: `transform: translateY(-2px)` + sombra
- Cards: `transform: translateY(-5px)` + sombra
- Pet Map: `transform: translateY(-2px)` + sombra

## Tipografía

### Familia de Fuentes
```css
font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
```

### Tamaños Estándar
| Elemento | Tamaño | Peso |
|----------|--------|------|
| h1 | 2.2rem | normal |
| h2 | 28px | 600 |
| Label | 14px | 600 |
| Párrafo | 15px | normal |
| Pequeño | 13px | normal |

## Sombras (Box-shadows)

| Nivel | Valor |
|-------|-------|
| Suave | `0 2px 4px rgba(0,0,0,0.1)` |
| Medio | `0 4px 10px rgba(0,0,0,0.1)` |
| Fuerte | `0 8px 20px rgba(0,0,0,0.15)` |
| Hover | `0 10px 25px rgba(102,126,234,0.3)` |

## Bordes (Border-radius)

| Tipo | Valor |
|------|-------|
| Inputs | 8px - 10px |
| Cards | 12px - 18px |
| Botones | 8px - 10px |
| Avatar | 50% (circular) |

## Flexbox / Grid Patterns

### Grid: 3fr/9fr (País/Teléfono)
```css
.form-row {
  display: grid;
  grid-template-columns: 3fr 9fr;
  gap: 12px;
}
```

### Grid: Auto-fit Responsive
```css
.field-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 14px;
}
```

### Grid: Cards 280px
```css
.mascotas-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}
```

### Flex: Botones
```css
.button-group {
  display: flex;
  gap: 10px;
}
.button-group > * { flex: 1; }
```

## Cómo Agregar Nueva Página CSS

### 1️⃣ Crear archivo CSS
```bash
# Crear assets/css/[feature-name].css
assets/css/my-page.css
```

### 2️⃣ Escribir estilos
```css
/* Nunca usar <style> inline en vistas */
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', sans-serif; background: #fdf6ff; }
/* ... más estilos */
```

### 3️⃣ Importar en vista
```html
<!-- En app/views/[path]/[page].php -->
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/my-page.css">
```

### 4️⃣ Nunca usar
```html
<!-- ❌ NO HACER ESTO -->
<style>
  .mi-clase { color: red; }
</style>
```

## Variables Globales (PHP)

```php
<?= ASSETS_URL ?>        // http://localhost/finalpp2/assets
<?= BASE_URL ?>          // http://localhost/finalpp2
<?= ROOT_PATH ?>         // Ruta absoluta del servidor
```

## Verificación de Cambios

Después de editar CSS:

1. **Limpiar caché del navegador** (F12 → Network → Disable cache)
2. **Verificar mobile** (F12 → Toggle device toolbar → 375px)
3. **Verificar gradientes** (Debe verse suave sin pixelación)
4. **Verificar animaciones** (slideUp, rise, hover effects)
5. **Verificar responsive** (Cambiar width, verificar media queries)

## Archivos a NO Importar

```css
/* ❌ Heredados - No usar en nuevas vistas */
iniciosesion.css
registro-usuario.css
registro-mascota-addon.css
perfil-usuario.css
busqueda.css
nousuario.css
home3_03.css
```

## Documentación Completa

- **CSS_ORGANIZATION.md** - Guía detallada de estructura
- **CSS_AUDIT_REPORT.md** - Reporte completo de auditoría
- Este archivo: **Quick Reference**

---

*Para preguntas, revisar CSS_ORGANIZATION.md o CSS_AUDIT_REPORT.md*

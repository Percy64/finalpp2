# Lost & Found Pet Management – Copilot Guide

Concise, actionable context to help AI agents work productively in this PHP/XAMPP app.

## Big picture
- PHP app for managing lost/found pets; runs on XAMPP (Apache + MySQL). No build step.
- Entry point (local): http://localhost/lost-found/home/index.php (not the project root).
- Domain folders: `home/`, `usuario/` (auth/profile), `mascota/` (pets), `includes/` (shared), `assets/` (static), `tools/` (debug), `legal/`.

## Architecture & conventions
- Always include shared pieces with relative paths: `require_once __DIR__ . '/../includes/conexion.php';` and `../includes/config.php`.
- `includes/conexion.php` creates a global `$pdo` (PDO) connection to MySQL database `mascotas_db`.
- `includes/config.php` defines `BASE_URL` and other path constants used by UI and nav.
- Authentication via PHP sessions; session keys: `usuario_id`, `usuario_nombre`, `usuario_email`.
- QR codes generated via `includes/QRGenerator.php` and point to `mascota/perfil_mascota.php?id={pet_id}`.
- Images are stored on disk under `assets/images/{mascotas|usuarios}/` with names `{type}_{user_id}_{timestamp}.{ext}` and referenced by DB field `foto_url`.

## Key files (patterns inside)
- `usuario/iniciosesion.php`: login + `password_verify`, sets session.
- `usuario/perfil_usuario.php`: gated by session, lists user pets.
- `mascota/registro_mascota.php`: create pet, upload image, generate QR.
- `mascota/perfil_mascota.php?id=`: public pet profile (target of QR).
- `mascota/cambiar_estado.php`: toggle `estado` in `mascotas` (`normal|perdida|encontrada`).
- `mascota/mapa.php`: search UI + Google Maps context.
- `includes/bottom_nav.php`: shared nav; uses `BASE_URL`, avoids starting sessions.
- `tools/debug_mascotas.php`: end-to-end debug (session, DB, schema, queries).

## Code patterns (copy these)
```php
// DB connection in any subfolder file
require_once __DIR__ . '/../includes/conexion.php'; // provides $pdo

// Guard routes that require login
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../usuario/iniciosesion.php'); exit;
}

// Form validation skeleton
$error = false; $msg = '';
if (isset($_POST['submit'])) {
  $name = trim($_POST['name'] ?? '');
  if ($name === '') { $msg = 'Nombre requerido'; $error = true; }
}

// QR code generation (after insert)
require_once __DIR__ . '/../includes/QRGenerator.php';
$qr = (new QRGenerator())->generateQR($id_mascota, $_SESSION['usuario_id']);
```

## Database model (essentials)
- DB: `mascotas_db` (MySQL/MariaDB, PDO).
- Tables: `usuarios` (auth, hashed passwords), `mascotas` (has `usuario_id`, `estado`, `foto_url`), `codigos_qr` (links to pets), `historial_medico` (by pet). `fotos_mascotas` is legacy.

## Local dev & data
- Quick start URL: http://localhost/lost-found/home/index.php
- Import schema (PowerShell, XAMPP default paths):
  - C:\xampp\mysql\bin\mysql.exe -u root mascotas_db < database\mascotas.sql
  - C:\xampp\mysql\bin\mysql.exe -u root mascotas_db < database\agregar_estado_mascotas.sql
- Or use http://localhost/phpmyadmin to import the SQL files.

## Gotchas & norms
- Use prepared statements; do not interpolate SQL.
- Use relative `require_once __DIR__ . '/..'` paths everywhere for portability.
- Store images on disk and persist the file path in `foto_url` (avoid BLOBs).
- Navigation relies on `BASE_URL`; keep routes folder-aware.
- Some UI actions (Info/Settings), email notifications, and camera/QR scanning are placeholders.

## Typical flows to implement/fix
- Add pet: validate -> save image -> insert in `mascotas` -> generate QR -> redirect to profile.
- Status change: POST to `mascota/cambiar_estado.php` with `estado` and pet id.
- Search: enhance filters/UI in `mascota/mapa.php`; keep Google Maps wiring minimal and key-free.
- Debug: if data “disappears,” run `tools/debug_mascotas.php` to verify session, DB, and FKs.

If anything here is unclear or missing for your task (e.g., exact payloads or UI states), ask to refine this guide with concrete examples from the target file.
<!--
# Lost & Found Pet Management System - AI Instructions

## Project Overview
This is a PHP-based web application for managing lost and found pets, built for XAMPP/WAMP local development. The system uses a MySQL database with PDO connections and follows a **folder-organized structure** with separated concerns. **Full authentication system** with session management and **QR code generation** are implemented.

## Architecture & Folder Structure
The project follows a **domain-based folder organization**:
```
├── home/                # Landing page and main feed
│   └── index.php        # Main entry point (not root-level home.php)
├── usuario/             # User management domain
│   ├── iniciosesion.php # Login form with authentication
│   ├── logout.php       # Session destruction 
│   ├── perfil_usuario.php # User profile with pet listings
│   ├── editar_perfil.php  # Profile editing
│   └── registro_usuario.php # User registration
├── mascota/             # Pet management domain
│   ├── registro_mascota.php # Pet registration with QR generation
│   ├── perfil_mascota.php   # Pet profile with GET parameter ?id=
│   ├── editar_mascota.php   # Pet editing functionality
│   ├── cambiar_estado.php   # Toggle lost/found status
│   ├── mapa.php            # Map search interface
│   └── qrinfo.php          # QR code information
├── includes/            # Shared components and configuration
│   ├── conexion.php     # PDO database connection
│   ├── config.php       # Base URL detection and constants
│   ├── QRGenerator.php  # QR code generation class
│   └── bottom_nav.php   # Shared navigation component
├── legal/               # Legal pages (terms, mission)
├── tools/               # Debug utilities
└── assets/              # Static assets organized by type
```

## Database Architecture
- **Primary Database**: `mascotas_db` with MySQL/MariaDB
- **Connection Pattern**: `require_once __DIR__ . '/../includes/conexion.php'` (relative paths)
- **Key Tables**:
  - `usuarios` - User accounts with hashed passwords and profile photos
  - `mascotas` - Pet records with `estado` field (normal/perdida/encontrada) and `id` foreign key
  - `codigos_qr` - QR codes linked to pets via `id_qr` foreign key
  - `historial_medico` - Medical history linked to pets
  - `fotos_mascotas` - Legacy table (photos now use `foto_url` field pointing to files)

## Development Environment
- **Server**: XAMPP (Apache + MySQL) on Windows
- **Database**: Access via `http://localhost/phpmyadmin`
- **Entry Point**: `http://localhost/lost-found/home/index.php` (not root)
- **No build tools**: Direct PHP execution, no compilation needed

## Critical Code Patterns

### Database Connection (All Files)
```php
// Modern pattern used throughout
require_once __DIR__ . '/../includes/conexion.php'; // Relative from subfolders
// Creates global $pdo variable for use
```

### URL/Path Configuration
```php
// includes/config.php provides auto-detection
require_once __DIR__ . '/../includes/config.php';
// Defines BASE_URL, ASSETS_URL constants for cross-references
```

### Form Validation Pattern
All forms follow this structure in `usuario/` and `mascota/` folders:
```php
$error = false;
$msg_field = '';

if(isset($_POST['btn_name'])){
    if(isset($_POST['field'])){
        $field = trim($_POST['field']);
        if(empty($field)){
            $msg_field = 'Error message';
            $error = true;
        }
    }
}
```

### Session-Based Authentication
```php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../usuario/iniciosesion.php'); // Relative paths important
    exit;
}
```

### QR Code Generation
```php
require_once __DIR__ . '/../includes/QRGenerator.php';
$qrGenerator = new QRGenerator();
$qrCode = $qrGenerator->generateQR($id_mascota, $usuario_id);
// Auto-detects base URL and generates QR pointing to perfil_mascota.php?id=
```

### Image Handling
- **Upload Method**: `$_FILES` with `move_uploaded_file()` to file system
- **Storage**: File-based storage in `assets/images/{mascotas|usuarios}/`
- **Naming Pattern**: `{type}_{user_id}_{timestamp}.{extension}`
- **Fallback**: Database still supports BLOB storage via `foto_url` field
- **Preview**: JavaScript for client-side image preview before upload

### Navigation Component
```php
// includes/bottom_nav.php - Shared across all pages
require_once __DIR__ . '/config.php';
// Uses BASE_URL constant for proper routing between folders
// Handles login state detection without starting sessions
```

## Key Implementation Details

### Authentication
- **Current State**: Full authentication system implemented with session management
- **Login**: `usuario/iniciosesion.php` handles credential verification and session creation
- **Sessions**: `$_SESSION['usuario_id']`, `$_SESSION['usuario_nombre']`, `$_SESSION['usuario_email']`
- **Protection**: Pages like `usuario/perfil_usuario.php` check session and redirect to login if not authenticated
- **Logout**: `usuario/logout.php` destroys session and redirects to login
- **Password Hashing**: Uses `password_hash()` and `password_verify()` for secure authentication

### QR Code System
- **Purpose**: Each pet gets a unique QR code for identification
- **Format**: URLs like `https://miproyecto.com/mascota/{id}`
- **Integration**: Links to `mascota/perfil_mascota.php?id={pet_id}`

### Pet Status Management
- **Estados**: `enum('normal', 'perdida', 'encontrada')` in `mascotas` table
- **Toggle**: `mascota/cambiar_estado.php` handles status changes
- **Tracking**: `fecha_estado` and `descripcion_estado` fields for history

### UI/UX Patterns
 - **Color Scheme**: Consistent light yellow background, hex FAF3B5
- **Mobile-First**: Max-width 430px containers
- **Navigation**: `includes/bottom_nav.php` with BASE_URL-aware routing
- **Form Style**: Centered forms with rounded corners and purple accent

## Critical Workflows

### Adding New Pets
1. User fills `mascota/registro_mascota.php` form (requires authentication)
2. Image processed via `move_uploaded_file()` to file system with unique naming
3. Data validated and inserted to `mascotas` table with `foto_url` field pointing to file path
4. QR code generated and linked

### Search and Navigation Integration
1. `mascota/mapa.php` provides map-based search interface
2. Search by name, species, breed, or color with real-time results
3. Google Maps integration for location context
4. Carousel navigation with smooth scrolling for pet cards
5. Unified navigation bar across all main pages (home, search, profile)

### User Profile Management
1. `usuario/perfil_usuario.php` displays user info and their registered pets
2. Session-protected with automatic redirect to login if not authenticated
3. Queries pets using `id` foreign key to link with `usuarios` table
4. `usuario/editar_perfil.php` allows updating user information and profile photo

### Debugging Database Issues
- Use `tools/debug_mascotas.php` to troubleshoot session, database connection, and data retrieval issues
- Shows session state, user verification, table structure, and query results
- Helpful for diagnosing foreign key relationships and data inconsistencies

### Database Setup
```sql
-- Import the complete schema
mysql -u root mascotas_db < database/mascotas.sql
-- Add estado fields
mysql -u root mascotas_db < database/agregar_estado_mascotas.sql
```

### Development Testing
- **Local Testing**: Use `http://localhost/lost-found/home/index.php`
- **Database**: Pre-populated with sample users and pets
- **Image Testing**: Upload forms expect image files, store as BLOB

## Missing Implementations
When extending this system, note these are placeholder/incomplete:
- Navigation functionality (currently `alert()` placeholders for Info and Settings)
- Email integration for lost pet notifications
- QR code generation and scanning functionality
- Camera integration for search by photo feature

## Database Relationships
```
usuarios (1) ←→ (N) mascotas ←→ (1) codigos_qr
    ↓
mascotas (1) ←→ (N) historial_medico
    ↓
mascotas (1) ←→ (N) fotos_mascotas (unused)
```

Always use prepared statements and maintain the existing validation patterns when adding new features.
-->
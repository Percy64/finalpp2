# Lost & Found Pet Management – Copilot Guide

Concise, actionable context to help AI agents work productively in this PHP/XAMPP app.

## Big picture
- PHP app for managing lost/found pets; runs on XAMPP (Apache + MySQL). No build step.
- **Architecture: MVC pattern** - All requests route through `public/index.php` front controller.
- Entry point (local): http://localhost/finalpp2/ (routes to public/index.php via .htaccess).
- Domain folders: `app/` (MVC structure: controllers, models, views, core, includes), `config/` (settings), `assets/` (static files).
- URL rewriting: `.htaccess` redirects everything to `public/index.php`, Router dispatches to controllers.

## Architecture & conventions
- **MVC Pattern**: Models in `app/models/`, Controllers in `app/controllers/`, Views in `app/views/`.
- **Front Controller**: All requests → `public/index.php` → Router → Controller → View.
- **Base Controller**: All controllers extend `app/core/Controller.php` with helpers: `view()`, `redirect()`, `requireAuth()`, `uploadImage()`.
- Always require config: `require_once __DIR__ . '/../../config/config.php';` and `config/database.php`.
- `config/database.php` provides singleton `Database::getInstance()->getConnection()` returning PDO.
- `config/config.php` auto-detects `BASE_URL` from `$_SERVER['SCRIPT_NAME']`, defines `ASSETS_URL`, `ROOT_PATH`, `PUBLIC_HOST`.
- Authentication via PHP sessions (started in `public/index.php`); session keys: `usuario_id`, `usuario_nombre`, `usuario_email`.
- QR codes generated via `app/includes/QRGenerator.php` using external API (qrserver.com).
- Images stored on disk under `assets/images/{mascotas|usuarios}/` with names `{type}_{user_id}_{timestamp}.{ext}`, referenced by DB field `foto_url`.

## Key files (patterns inside)
- `public/index.php`: Front controller, defines all routes, dispatches to controllers.
- `app/core/Router.php`: URL routing with regex patterns, supports `{id}` params.
- `app/core/Controller.php`: Base class with `view()`, `redirect()`, `requireAuth()`, `uploadImage()`, `json()`.
- `app/controllers/UserController.php`: login, register, profile, editProfile, logout, recoverPassword, resetPassword.
- `app/controllers/PetController.php`: register, profile, edit, delete, changeStatus, map, qrInfo.
- `app/controllers/HomeController.php`: index (shows lost pets).
- `app/models/User.php`: findById, findByEmail, create, update, delete, getPets, authenticate.
- `app/models/Pet.php`: findById, findByUserId, findLostPets, create, update, delete, changeStatus, getStatistics.
- `app/views/home/index.php`: main page showing lost pets.
- `app/views/user/`: login, register, profile, edit_profile, recover_password, reset_password.
- `app/views/pet/`: register, profile, edit, delete, map, qr_info.
- `app/views/legal/`: terminos-condiciones, mision_vision.
- `app/includes/bottom_nav.php`: shared nav; uses `BASE_URL`, updated for MVC routes.
- `app/includes/QRGenerator.php`: QR code generation via external API.
- `tools/debug_mascotas.php`: end-to-end debug (session, DB, schema, queries).

## Code patterns (copy these)
```php
// Controller pattern
class UserController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->loadModel('User');
    }
    
    public function profile() {
        $this->requireAuth(); // Guard route
        $user = $this->userModel->findById($_SESSION['usuario_id']);
        $this->view('user/profile', ['user' => $user]);
    }
}

// Model pattern
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

// Route definition (public/index.php)
$router->get('/perfil', 'UserController', 'profile');
$router->get('/mascota/{id}', 'PetController', 'profile');

// View rendering
$this->view('user/profile', ['user' => $user, 'pets' => $pets]);

// Image upload in controller
$foto_url = $this->uploadImage($_FILES['foto'], 'mascota', $usuario_id);

// Redirect pattern
$this->redirect('/perfil');

// JSON response
## Local dev & data
- Quick start URL: http://localhost/finalpp2/ (routes to public/index.php)
- RewriteBase in `.htaccess`: `/finalpp2/` (adjust if folder name changes)
- All requests go through front controller: `public/index.php`
- Import schema (PowerShell, XAMPP default paths):
  ```powershell
  C:\xampp\mysql\bin\mysql.exe -u root mascotas_db < database\mascotas.sql
  C:\xampp\mysql\bin\mysql.exe -u root mascotas_db < database\password_resets.sql
  ```
- Or use http://localhost/phpmyadmin to import SQL files.
- Friendly URLs via Router: `/login`, `/mascota/123`, `/mapa`, etc.
- **Clean MVC structure**: Legacy folders removed, only MVC pattern files remain.
- DB: `mascotas_db` (MySQL/MariaDB, PDO).
- Tables: `usuarios` (auth, hashed passwords), `mascotas` (has FK `id` to usuarios, `estado` enum, `foto_url`, `id_qr` FK), `codigos_qr` (QR metadata), `historial_medico` (by pet), `mascotas_perdidas` (enhanced location tracking), `password_resets` (token-based reset with expiry), `verificaciones_whatsapp` (placeholder). `fotos_mascotas` is legacy.
- FKs: `mascotas.id` → `usuarios.id`, `mascotas.id_qr` → `codigos_qr.id_qr`, `historial_medico.id_mascota` → `mascotas.id_mascota`.

## Local dev & data
- Quick start URL: http://localhost/finalpp2/home/index.php (or just http://localhost/finalpp2)
- RewriteBase in `.htaccess`: `/finalpp2/` (adjust if folder name changes)
- Import schema (PowerShell, XAMPP default paths):
  ```powershell
  C:\xampp\mysql\bin\mysql.exe -u root mascotas_db < database\mascotas.sql
  C:\xampp\mysql\bin\mysql.exe -u root mascotas_db < database\password_resets.sql
  ```
- Or use http://localhost/phpmyadmin to import SQL files.
## Gotchas & norms
- Use Model methods; avoid direct DB queries in controllers when possible.
- Controllers must extend `Controller` base class.
- Use `$this->view()`, `$this->redirect()`, `$this->requireAuth()` in controllers.
- Store images on disk via `$this->uploadImage()` helper, persist path in `foto_url`.
- Navigation relies on `BASE_URL`; all routes defined in `public/index.php`.
- `.htaccess` RewriteBase is `/finalpp2/` – update if project folder renamed.
- Password reset: tokens expire in 1 hour, stored in `password_resets` table with `used` flag.
- Delete operations: use Model methods that handle cascading (historial_medico → mascotas → codigos_qr) and unlink physical files.
- Sessions auto-started in `public/index.php`; access via `$_SESSION` anywhere.
- QR codes use external API (qrserver.com), no API key needed, saves to `assets/images/qr/`.
## Typical flows to implement/fix
- **Add feature**: Create/update Model → Create/update Controller method → Create/update View → Add route in `public/index.php`.
- **Add pet**: UserController requires auth → PetController::register validates → Pet Model creates → QR generated → redirect to profile.
- **Status change**: POST to `/mascota/{id}/cambiar-estado` → PetController::changeStatus → Pet Model updates `estado`.
- **Password reset**: user enters email → UserController creates token in `password_resets` → email link (mock: show URL) → validate token + expiry → update password + mark token used.
- **Delete pet**: verify ownership in controller → Pet Model handles cascading delete (photo file, historial_medico, mascotas, codigos_qr).
- **Search**: enhance PetController::map and `app/views/pet/map.php`; keep Google Maps wiring minimal and key-free.
- **Debug**: if data "disappears," run `tools/debug_mascotas.php` or check Model methods for FK issues.
## URL routing (Router in `public/index.php`)
- `/` or `/home` → HomeController::index
- `/login` (GET/POST) → UserController::login
- `/registro` (GET/POST) → UserController::register
- `/perfil` → UserController::profile (requires auth)
- `/editar-perfil` → UserController::editProfile (requires auth)
- `/logout` → UserController::logout
- `/recuperar-password` → UserController::recoverPassword
- `/reset-password?email=&token=` → UserController::resetPassword
- `/registrar-mascota` → PetController::register (requires auth)
- `/mascota/{id}` → PetController::profile
- `/mascota/{id}/editar` → PetController::edit (requires auth)
- `/mascota/{id}/eliminar` → PetController::delete (requires auth)
- `/mascota/{id}/cambiar-estado` (POST) → PetController::changeStatus (requires auth)
- `/mapa` → PetController::map
- `/qr/{id}` → PetController::qrInfo
- `/usuario/{id}` → UserController::profile (public view)
- `/terminos` → LegalController::terminos
- `/mision` → LegalController::mision

**Adding new routes**: Edit `public/index.php`, add `$router->get('/path', 'ControllerName', 'methodName');`ar_mascota.php?id=123`
- `/mascota/123/eliminar` → `mascota/eliminar_mascota.php?id=123`
- `/qr/123` → `mascota/qrinfo.php?id=123`
- `/usuario/123` → `usuario/perfil_usuario.php?id=123`
- `/terminos` → `legal/terminos-condiciones.html`
- `/mision` → `legal/mision_vision.php`

If anything here is unclear or missing for your task (e.g., exact payloads or UI states), ask to refine this guide with concrete examples from the target file.
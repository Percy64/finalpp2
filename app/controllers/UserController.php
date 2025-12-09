<?php
/**
 * User Controller
 */
class UserController extends Controller {
    
    private $userModel;

    public function __construct() {
        $this->userModel = $this->loadModel('User');
    }

    public function login() {
        if ($this->isAuthenticated()) {
            $this->redirect('/perfil');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $error = 'Email y contraseña son requeridos';
            } else {
                $user = $this->userModel->authenticate($email, $password);
                if ($user) {
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['usuario_nombre'] = $user['nombre'];
                    $_SESSION['usuario_email'] = $user['email'];
                    
                    $redirect = $_SESSION['redirect_after_login'] ?? '/perfil';
                    unset($_SESSION['redirect_after_login']);
                    $this->redirect($redirect);
                } else {
                    $error = 'Credenciales incorrectas';
                }
            }
            
            $this->view('user/login', ['error' => $error ?? null, 'email' => $email]);
        } else {
            $this->view('user/login');
        }
    }

    public function register() {
        if ($this->isAuthenticated()) {
            $this->redirect('/perfil');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'apellido' => trim($_POST['apellido'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'codigo_pais' => trim($_POST['codigo_pais'] ?? '+54'),
                'direccion' => trim($_POST['direccion'] ?? '')
            ];

            $errors = [];
            
            if (empty($data['nombre'])) $errors[] = 'El nombre es requerido';
            if (empty($data['apellido'])) $errors[] = 'El apellido es requerido';
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email inválido';
            }
            if (empty($data['password']) || strlen($data['password']) < 6) {
                $errors[] = 'La contraseña debe tener al menos 6 caracteres';
            }

            // Verificar si email existe
            if (empty($errors) && $this->userModel->findByEmail($data['email'])) {
                $errors[] = 'El email ya está registrado';
            }

            // Upload foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $foto = $this->uploadImage($_FILES['foto'], 'usuario');
                if ($foto) {
                    $data['foto_url'] = $foto;
                }
            }

            if (empty($errors)) {
                $userId = $this->userModel->create($data);
                $_SESSION['usuario_id'] = $userId;
                $_SESSION['usuario_nombre'] = $data['nombre'];
                $_SESSION['usuario_email'] = $data['email'];
                $this->redirect('/perfil');
            }

            $this->view('user/register', ['errors' => $errors, 'data' => $data]);
        } else {
            $this->view('user/register');
        }
    }

    public function profile($userId = null) {
        $this->requireAuth();
        
        $currentUser = $this->getCurrentUser();
        $userId = $userId ?? $currentUser['id'];
        
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->redirect('/');
        }

        $pets = $this->userModel->getPets($userId);
        
        $this->view('user/profile', [
            'user' => $user,
            'pets' => $pets,
            'isOwnProfile' => ($userId == $currentUser['id'])
        ]);
    }

    public function editProfile() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $user = $this->userModel->findById($currentUser['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'apellido' => trim($_POST['apellido'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'codigo_pais' => trim($_POST['codigo_pais'] ?? '+54'),
                'direccion' => trim($_POST['direccion'] ?? '')
            ];

            // Upload foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $foto = $this->uploadImage($_FILES['foto'], 'usuario', $currentUser['id']);
                if ($foto) {
                    $data['foto_url'] = $foto;
                }
            }

            // Cambiar contraseña
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) >= 6) {
                    $data['password'] = $_POST['password'];
                } else {
                    $error = 'La contraseña debe tener al menos 6 caracteres';
                }
            }

            if (!isset($error)) {
                $this->userModel->update($currentUser['id'], $data);
                $_SESSION['usuario_nombre'] = $data['nombre'];
                $_SESSION['success_message'] = 'Perfil actualizado correctamente';
                $this->redirect('/perfil');
            }

            $this->view('user/edit_profile', ['user' => array_merge($user, $data), 'error' => $error]);
        } else {
            $this->view('user/edit_profile', ['user' => $user]);
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/');
    }

    public function recoverPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email inválido';
            } else {
                $user = $this->userModel->findByEmail($email);
                $token = bin2hex(random_bytes(16));
                $expires = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');
                
                $db = Database::getInstance()->getConnection();
                if ($user) {
                    $stmt = $db->prepare('INSERT INTO password_resets (usuario_id, email, token, expires_at, used) VALUES (?, ?, ?, ?, 0)');
                    $stmt->execute([$user['id'], $email, $token, $expires]);
                } else {
                    $stmt = $db->prepare('INSERT INTO password_resets (usuario_id, email, token, expires_at, used) VALUES (NULL, ?, ?, ?, 0)');
                    $stmt->execute([$email, $token, $expires]);
                }
                
                $resetUrl = BASE_URL . '/reset-password?email=' . urlencode($email) . '&token=' . urlencode($token);
                $message = 'Si el email existe, te enviaremos un enlace. Para pruebas: ' . $resetUrl;
            }
            
            $this->view('user/recover_password', ['message' => $message ?? null, 'error' => $error ?? null]);
        } else {
            $this->view('user/recover_password');
        }
    }

    public function resetPassword() {
        $email = trim($_GET['email'] ?? '');
        $token = trim($_GET['token'] ?? '');
        
        if (empty($email) || empty($token)) {
            $this->redirect('/login');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM password_resets WHERE email = ? AND token = ? LIMIT 1');
        $stmt->execute([$email, $token]);
        $resetRow = $stmt->fetch();
        
        if (!$resetRow || $resetRow['used'] == 1) {
            $error = 'Enlace inválido o ya usado';
        } else {
            $now = new DateTime();
            $exp = new DateTime($resetRow['expires_at']);
            if ($now > $exp) {
                $error = 'El enlace ha expirado';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($error)) {
            $password = trim($_POST['password'] ?? '');
            $confirm = trim($_POST['confirm'] ?? '');
            
            if (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } elseif ($password !== $confirm) {
                $error = 'Las contraseñas no coinciden';
            } else {
                $user = $this->userModel->findByEmail($email);
                if ($user) {
                    $this->userModel->update($user['id'], ['password' => $password]);
                    $stmt = $db->prepare('UPDATE password_resets SET used = 1 WHERE id = ?');
                    $stmt->execute([$resetRow['id']]);
                    $_SESSION['success_message'] = 'Contraseña actualizada correctamente';
                    $this->redirect('/login');
                } else {
                    $error = 'Usuario no encontrado';
                }
            }
        }

        $this->view('user/reset_password', ['email' => $email, 'token' => $token, 'error' => $error ?? null]);
    }
}

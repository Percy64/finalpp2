<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php';

// Nota: Implementación mínima. El token NO se persiste aún.
// Flujo esperado completo: guardar token en DB con expiración y validarlo.

$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');

$msg = '';
$error = false;

// Validación básica de acceso a la página
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $token === '') {
    $msg = 'Enlace inválido o incompleto.';
    $error = true;
}

// Cargar y validar token antes de permitir POST
$tokenValid = false;
$resetRow = null;
if (!$error) {
    try {
        $sel = $pdo->prepare('SELECT id, usuario_id, email, token, expires_at, used FROM password_resets WHERE email = ? AND token = ? LIMIT 1');
        $sel->execute([$email, $token]);
        $resetRow = $sel->fetch(PDO::FETCH_ASSOC);
        if ($resetRow) {
            $now = new DateTime();
            $exp = new DateTime($resetRow['expires_at']);
            if ((int)$resetRow['used'] === 0 && $now <= $exp) {
                $tokenValid = true;
            } else {
                $msg = 'El enlace ha expirado o ya fue utilizado.';
                $error = true;
            }
        } else {
            $msg = 'Enlace inválido.';
            $error = true;
        }
    } catch (PDOException $e) {
        $msg = 'Ocurrió un error al validar el enlace.';
        $error = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error && $tokenValid) {
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm'] ?? '');

    if ($password === '' || $confirm === '') {
        $msg = 'Debes ingresar y confirmar la nueva contraseña.';
        $error = true;
    } elseif (strlen($password) < 6) {
        $msg = 'La contraseña debe tener al menos 6 caracteres.';
        $error = true;
    } elseif ($password !== $confirm) {
        $msg = 'Las contraseñas no coinciden.';
        $error = true;
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $msg = 'Enlace inválido.';
                $error = true;
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $pdo->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
                $upd->execute([$hash, $user['id']]);

                // Marcar token como usado
                $updToken = $pdo->prepare('UPDATE password_resets SET used = 1 WHERE id = ?');
                $updToken->execute([$resetRow['id']]);

                // Mensaje de éxito y redirección al login
                $_SESSION['reset_success'] = 'Tu contraseña fue actualizada correctamente.';
                header('Location: ' . BASE_URL . '/usuario/iniciosesion.php');
                exit;
            }
        } catch (PDOException $e) {
            $msg = 'Ocurrió un error al actualizar tu contraseña. Intenta nuevamente más tarde.';
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Restablecer contraseña</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/iniciosesion.css" />
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" method="post" action="">
            <div class="logo-container">
                <img src="<?= ASSETS_URL ?>/images/logo.png" alt="Logo" class="logo" />
            </div>
            <h2>Restablecer contraseña</h2>
            <p class="page-subtitle">Ingresa tu nueva contraseña para tu cuenta asociada a <?= htmlspecialchars($email) ?>.</p>

            <?php if ($msg !== ''): ?>
                <div class="<?= $error ? 'error-message' : 'success-message' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <div>
                <input type="password" name="password" placeholder="Nueva contraseña" required />
            </div>
            <div>
                <input type="password" name="confirm" placeholder="Confirmar contraseña" required />
            </div>

            <button type="submit" class="btn_enviar">Actualizar contraseña</button>

            <div class="register-link" style="margin-top:10px; text-align:center;">
                <a href="<?= BASE_URL ?>/usuario/iniciosesion.php">Volver al inicio de sesión</a>
            </div>
        </form>
    </section>
</body>
</html>

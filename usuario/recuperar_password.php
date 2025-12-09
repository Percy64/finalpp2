<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php';

$msg = '';
$error = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email === '') {
        $msg = 'El email es obligatorio.';
        $error = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'El email no es válido.';
        $error = true;
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, nombre, email FROM usuarios WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Siempre respondemos genéricamente, pero si existe el usuario, persistimos token
            $token = bin2hex(random_bytes(16));
            $expires = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');
            if ($user) {
                $ins = $pdo->prepare('INSERT INTO password_resets (usuario_id, email, token, expires_at, used) VALUES (?, ?, ?, ?, 0)');
                $ins->execute([$user['id'], $email, $token, $expires]);
            } else {
                // Si no existe usuario, aún guardamos registro con usuario_id NULL para trazabilidad opcional
                $ins = $pdo->prepare('INSERT INTO password_resets (usuario_id, email, token, expires_at, used) VALUES (NULL, ?, ?, ?, 0)');
                $ins->execute([$email, $token, $expires]);
            }
            // Mensaje genérico (y mostrar enlace directo para pruebas locales)
            $resetUrl = BASE_URL . '/usuario/reset_password.php?email=' . urlencode($email) . '&token=' . urlencode($token);
            $msg = 'Si el email existe, te enviaremos un enlace de recuperación. Para pruebas locales: ' . $resetUrl;
        } catch (PDOException $e) {
            $msg = 'Ocurrió un error. Intenta nuevamente más tarde.';
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
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/iniciosesion.css" />
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" method="post" action="">
            <div class="logo-container">
                <img src="<?= ASSETS_URL ?>/images/logo.png" alt="Logo" class="logo" />
            </div>
            <h2>Recuperar contraseña</h2>
            <p class="page-subtitle">Ingresa tu email y te enviaremos un enlace para restablecerla.</p>

            <?php if ($msg !== ''): ?>
                <div class="<?= $error ? 'error-message' : 'success-message' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <div>
                <input type="email" name="email" placeholder="Tu email" value="<?= htmlspecialchars($email) ?>" required />
            </div>

            <button type="submit" class="btn_enviar">Enviar enlace</button>

            <div class="register-link" style="margin-top:10px; text-align:center;">
                <a href="<?= BASE_URL ?>/usuario/iniciosesion.php">Volver al inicio de sesión</a>
            </div>
        </form>
    </section>
</body>
</html>

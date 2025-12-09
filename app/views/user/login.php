<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Alert - Iniciar Sesión</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/user-login.css">
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" action="<?= BASE_URL ?>/login" method="post">
            <div class="logo-container">
                <img src="<?= ASSETS_URL ?>/images/logo.png" alt="Logo" class="logo">
            </div>

            <h2>Bienvenido</h2>
            <p class="page-subtitle">Inicia sesión para continuar</p>

            <?php if(isset($error)): ?>
                <div class="error-message">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    ✓ <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <input type="email" name="email" placeholder="Correo electrónico" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>

            <button type="submit" name="login_btn" class="btn_enviar">🔑 Iniciar sesión</button>
            
            <div class="register-link">
                <p>¿No tienes cuenta? <a href="<?= BASE_URL ?>/registro">Regístrate aquí</a></p>
            </div>

            <div class="recover-link">
                <a href="<?= BASE_URL ?>/recuperar-password">¿Olvidaste tu contraseña?</a>
            </div>
        </form>
    </section>
</body>
</html>
    </section>
</body>
</html>

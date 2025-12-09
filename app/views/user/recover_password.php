<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" action="<?= BASE_URL ?>/recuperar-password" method="post">
            <h2>Recuperar contraseña</h2>
            <p>Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña.</p>

            <?php if(isset($message)): ?>
                <div class="success-message" style="background-color:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div>
                <input type="email" name="email" placeholder="Tu email" required>
            </div>

            <button type="submit" class="btn_enviar">Enviar enlace</button>
            
            <div class="register-link">
                <p><a href="<?= BASE_URL ?>/login">Volver al login</a></p>
            </div>
        </form>
    </section>
</body>
</html>

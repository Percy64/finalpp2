<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" action="<?= BASE_URL ?>/reset-password?email=<?= urlencode($email) ?>&token=<?= urlencode($token) ?>" method="post">
            <h2>Restablecer contraseña</h2>

            <?php if(isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div>
                <label>Nueva contraseña:</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
            </div>

            <div>
                <label>Confirmar contraseña:</label>
                <input type="password" name="confirm" placeholder="Repetir contraseña" required>
            </div>

            <button type="submit" class="btn_enviar">Cambiar contraseña</button>
        </form>
    </section>
</body>
</html>

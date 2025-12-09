<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/user-register.css">
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" action="<?= BASE_URL ?>/registro" method="post" enctype="multipart/form-data">
            <div class="logo-container">
                <img src="<?= ASSETS_URL ?>/images/logo.png" alt="Logo" class="logo">
            </div>

            <h2>Crear Cuenta</h2>
            <p class="page-subtitle">Únete a nuestra comunidad de mascotas</p>

            <?php if(!empty($errors)): ?>
                <div class="error-message">
                    <ul style="margin:0; padding-left:20px;">
                        <?php foreach($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <input type="text" name="nombre" placeholder="Nombre" value="<?= htmlspecialchars($data['nombre'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <input type="text" name="apellido" placeholder="Apellido" value="<?= htmlspecialchars($data['apellido'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Correo electrónico" value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña (mínimo 6 caracteres)" required>
            </div>

            <div class="form-row">
                <div class="form-row-col">
                    <label for="codigo_pais">Código de país:</label>
                    <select id="codigo_pais" name="codigo_pais" style="width:100%; padding:12px 15px; border:2px solid #e0e0e0; border-radius:8px; font-size:15px; font-family:inherit; transition:all 0.3s ease; cursor:pointer;">
                        <option value="+54" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+54') ? 'selected' : '' ?>>🇦🇷 Argentina (+54)</option>
                        <option value="+55" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+55') ? 'selected' : '' ?>>🇧🇷 Brasil (+55)</option>
                        <option value="+56" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+56') ? 'selected' : '' ?>>🇨🇱 Chile (+56)</option>
                        <option value="+57" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+57') ? 'selected' : '' ?>>🇨🇴 Colombia (+57)</option>
                        <option value="+34" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+34') ? 'selected' : '' ?>>🇪🇸 España (+34)</option>
                        <option value="+51" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+51') ? 'selected' : '' ?>>🇵🇪 Perú (+51)</option>
                        <option value="+598" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+598') ? 'selected' : '' ?>>🇺🇾 Uruguay (+598)</option>
                        <option value="+58" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+58') ? 'selected' : '' ?>>🇻🇪 Venezuela (+58)</option>
                        <option value="+502" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+502') ? 'selected' : '' ?>>🇬🇹 Guatemala (+502)</option>
                        <option value="+1" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+1') ? 'selected' : '' ?>>🇺🇸 USA/Canadá (+1)</option>
                        <option value="+44" <?= (isset($data['codigo_pais']) && $data['codigo_pais'] === '+44') ? 'selected' : '' ?>>🇬🇧 Reino Unido (+44)</option>
                    </select>
                </div>

                <div class="form-row-col">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="Teléfono (opcional)" value="<?= htmlspecialchars($data['telefono'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <textarea name="direccion" placeholder="Dirección (opcional)" rows="3"><?= htmlspecialchars($data['direccion'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>📷 Foto de perfil (opcional)</label>
                <input type="file" name="foto" accept="image/*">
            </div>

            <button type="submit" name="env_btn" class="btn_enviar">✓ Registrarse</button>
            
            <div class="register-link">
                <p>¿Ya tienes cuenta? <a href="<?= BASE_URL ?>/login">Inicia sesión aquí</a></p>
            </div>
        </form>
    </section>
</body>
</html>

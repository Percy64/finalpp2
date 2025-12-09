<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/user-edit-profile.css">
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" action="<?= BASE_URL ?>/editar-perfil" method="post" enctype="multipart/form-data">
            <h2>Editar Perfil</h2>

            <?php if(isset($error)): ?>
                <div class="error-message">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div>
                <label for="nombre">📝 Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
            </div>

            <div>
                <label for="apellido">🧾 Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($user['apellido'] ?? '') ?>" required>
            </div>

            <div>
                <label for="telefono">📱 Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($user['telefono'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-row-col">
                    <label for="codigo_pais">🌎 Código de país:</label>
                    <select id="codigo_pais" name="codigo_pais">
                        <option value="+54" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+54') ? 'selected' : '' ?>>🇦🇷 Argentina (+54)</option>
                        <option value="+55" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+55') ? 'selected' : '' ?>>🇧🇷 Brasil (+55)</option>
                        <option value="+56" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+56') ? 'selected' : '' ?>>🇨🇱 Chile (+56)</option>
                        <option value="+57" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+57') ? 'selected' : '' ?>>🇨🇴 Colombia (+57)</option>
                        <option value="+34" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+34') ? 'selected' : '' ?>>🇪🇸 España (+34)</option>
                        <option value="+51" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+51') ? 'selected' : '' ?>>🇵🇪 Perú (+51)</option>
                        <option value="+598" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+598') ? 'selected' : '' ?>>🇺🇾 Uruguay (+598)</option>
                        <option value="+58" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+58') ? 'selected' : '' ?>>🇻🇪 Venezuela (+58)</option>
                        <option value="+502" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+502') ? 'selected' : '' ?>>🇬🇹 Guatemala (+502)</option>
                        <option value="+1" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+1') ? 'selected' : '' ?>>🇺🇸 USA/Canadá (+1)</option>
                        <option value="+44" <?= (isset($user['codigo_pais']) && $user['codigo_pais'] === '+44') ? 'selected' : '' ?>>🇬🇧 Reino Unido (+44)</option>
                    </select>
                </div>

                <div class="form-row-col">
                    <label for="telefono-row">📱 Teléfono:</label>
                    <input type="tel" id="telefono-row" name="telefono" value="<?= htmlspecialchars($user['telefono'] ?? '') ?>">
                </div>
            </div>

            <div>
                <label for="direccion">📍 Dirección:</label>
                <textarea id="direccion" name="direccion"><?= htmlspecialchars($user['direccion'] ?? '') ?></textarea>
            </div>

            <div>
                <label for="foto">📷 Nueva foto de perfil:</label>
                <input type="file" id="foto" name="foto" accept="image/*">
            </div>

            <div>
                <label for="password">🔑 Nueva contraseña (dejar en blanco para no cambiar):</label>
                <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres">
            </div>

            <div class="button-group">
                <button type="submit" class="btn_enviar">✓ Guardar Cambios</button>
                <button type="button" class="btn-cancelar" onclick="window.location.href='<?= BASE_URL ?>/perfil'">✕ Cancelar</button>
            </div>
        </form>
    </section>
</body>
</html>

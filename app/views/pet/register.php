<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Mascota</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/pet-register.css">
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" action="<?= BASE_URL ?>/registrar-mascota" method="post" enctype="multipart/form-data">
            <h2>Registrar Mascota</h2>
            <p class="subtitle">Completa los datos para generar el QR y el perfil de tu mascota</p>

            <?php if(!empty($errors)): ?>
                <div class="error-message">
                    <ul style="margin:0; padding-left:20px;">
                        <?php foreach($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="field-row spacer">
                <div>
                    <label>Nombre de la mascota:</label>
                    <input type="text" name="nombre" placeholder="Ej: Max" value="<?= htmlspecialchars($data['nombre'] ?? '') ?>" required>
                </div>

                <div>
                    <label>Especie:</label>
                    <select name="especie" required>
                        <option value="">Seleccione...</option>
                        <option value="perro" <?= (isset($data['especie']) && $data['especie'] === 'perro') ? 'selected' : '' ?>>Perro</option>
                        <option value="gato" <?= (isset($data['especie']) && $data['especie'] === 'gato') ? 'selected' : '' ?>>Gato</option>
                        <option value="ave" <?= (isset($data['especie']) && $data['especie'] === 'ave') ? 'selected' : '' ?>>Ave</option>
                        <option value="otro" <?= (isset($data['especie']) && $data['especie'] === 'otro') ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
            </div>

            <div class="field-row spacer">
                <div>
                    <label>Raza (opcional):</label>
                    <input type="text" name="raza" placeholder="Ej: Labrador" value="<?= htmlspecialchars($data['raza'] ?? '') ?>">
                </div>

                <div>
                    <label>Edad:</label>
                    <input type="number" name="edad" placeholder="Años" value="<?= htmlspecialchars($data['edad'] ?? '') ?>" min="0" max="30">
                </div>
            </div>

            <div class="field-row spacer">
                <div>
                    <label>Color (opcional):</label>
                    <input type="text" name="color" placeholder="Ej: Marrón con blanco" value="<?= htmlspecialchars($data['color'] ?? '') ?>">
                </div>

                <div>
                    <label>Género:</label>
                    <select name="genero">
                        <option value="">Seleccione...</option>
                        <option value="macho" <?= (isset($data['genero']) && $data['genero'] === 'macho') ? 'selected' : '' ?>>Macho</option>
                        <option value="hembra" <?= (isset($data['genero']) && $data['genero'] === 'hembra') ? 'selected' : '' ?>>Hembra</option>
                    </select>
                </div>
            </div>

            <div>
                <label>Descripción (opcional):</label>
                <textarea name="descripcion" rows="4" placeholder="Características especiales, comportamiento, etc."><?= htmlspecialchars($data['descripcion'] ?? '') ?></textarea>
            </div>

            <div>
                <label>Foto de la mascota:</label>
                <input type="file" name="foto" accept="image/*">
            </div>

            <div class="btn-row">
                <button type="submit" name="btn_enviar" class="btn_enviar">Registrar Mascota</button>
                <button type="button" onclick="window.location.href='<?= BASE_URL ?>/perfil'" class="btn-cancelar">Cancelar</button>
            </div>
        </form>
    </section>
</body>
</html>

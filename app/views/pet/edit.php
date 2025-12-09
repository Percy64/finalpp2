<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mascota</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/pet-register.css">
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" action="<?= BASE_URL ?>/mascota/<?= $pet['id_mascota'] ?>/editar" method="post" enctype="multipart/form-data">
            <h2>Editar Mascota</h2>

            <div>
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($pet['nombre']) ?>" required>
            </div>

            <div>
                <label>Especie:</label>
                <select name="especie" required>
                    <option value="perro" <?= $pet['especie'] === 'perro' ? 'selected' : '' ?>>Perro</option>
                    <option value="gato" <?= $pet['especie'] === 'gato' ? 'selected' : '' ?>>Gato</option>
                    <option value="ave" <?= $pet['especie'] === 'ave' ? 'selected' : '' ?>>Ave</option>
                    <option value="otro" <?= $pet['especie'] === 'otro' ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>

            <div>
                <label>Raza:</label>
                <input type="text" name="raza" value="<?= htmlspecialchars($pet['raza'] ?? '') ?>">
            </div>

            <div>
                <label>Edad:</label>
                <input type="number" name="edad" value="<?= htmlspecialchars($pet['edad'] ?? '') ?>" min="0" max="30">
            </div>

            <div>
                <label>Color:</label>
                <input type="text" name="color" value="<?= htmlspecialchars($pet['color'] ?? '') ?>">
            </div>

            <div>
                <label>Género:</label>
                <select name="genero">
                    <option value="">Seleccione...</option>
                    <option value="macho" <?= ($pet['genero'] ?? '') === 'macho' ? 'selected' : '' ?>>Macho</option>
                    <option value="hembra" <?= ($pet['genero'] ?? '') === 'hembra' ? 'selected' : '' ?>>Hembra</option>
                </select>
            </div>

            <div>
                <label>Descripción:</label>
                <textarea name="descripcion" rows="4"><?= htmlspecialchars($pet['descripcion'] ?? '') ?></textarea>
            </div>

            <div>
                <label>Nueva foto:</label>
                <input type="file" name="foto" accept="image/*">
            </div>

            <button type="submit" class="btn_enviar">Guardar Cambios</button>
            <button type="button" onclick="window.location.href='<?= BASE_URL ?>/mascota/<?= $pet['id_mascota'] ?>'" class="btn-cancelar">Cancelar</button>
        </form>
    </section>
</body>
</html>

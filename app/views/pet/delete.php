<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Mascota</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
</head>
<body>
    <section class="registro-mascota">
        <div class="formulario" style="max-width: 420px; text-align: center;">
            <h2>Eliminar Mascota</h2>
            
            <div class="delete-confirm" style="padding: 20px;">
                <p style="font-size: 48px; margin: 20px 0;">⚠️</p>
                <h3 style="margin-bottom: 10px;">¿Estás seguro?</h3>
                <p style="color: #666; margin-bottom: 20px;">
                    Esta acción eliminará permanentemente a <strong><?= htmlspecialchars($pet['nombre']) ?></strong> y toda su información.
                    Esta acción no se puede deshacer.
                </p>
                
                <form action="<?= BASE_URL ?>/mascota/<?= $pet['id_mascota'] ?>/eliminar" method="post" style="display: flex; gap: 10px; justify-content: center;">
                    <button type="submit" name="confirmar_eliminar" class="btn-eliminar" style="background: #d32f2f; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;">
                        Sí, Eliminar
                    </button>
                    <button type="button" onclick="window.location.href='<?= BASE_URL ?>/perfil'" class="btn-cancelar" style="background: #6b4fd2; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;">
                        Cancelar
                    </button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php';

function obtenerTelefonoLocal($numero)
{
    $digitos = preg_replace('/\D+/', '', (string) $numero);
    if ($digitos === '') {
        return '';
    }
    if (substr($digitos, 0, 2) === '54') {
        $digitos = substr($digitos, 2);
    }
    return $digitos;
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: iniciosesion.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$error = false;
$success_message = '';
$msg_nombre = '';
$msg_apellido = '';
$msg_email = '';
$msg_telefono = '';
$msg_direccion = '';
$msg_foto = '';

$telefono_input = '';
$telefono_normalizado = null;

// Obtener datos actuales del usuario
try {
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        session_destroy();
        header('Location: iniciosesion.php');
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $telefono_input = obtenerTelefonoLocal($usuario['telefono'] ?? '');
    }
} catch(PDOException $e) {
    $error_message = 'Error al cargar los datos del usuario.';
}

// Procesar el formulario si se envía
if (isset($_POST['btn_actualizar'])) {
    
    // Validar nombre
    if (isset($_POST['nombre'])) {
        $nombre = trim($_POST['nombre']);
        if (empty($nombre)) {
            $msg_nombre = 'El nombre es obligatorio.';
            $error = true;
        } elseif (strlen($nombre) < 2) {
            $msg_nombre = 'El nombre debe tener al menos 2 caracteres.';
            $error = true;
        }
    } else {
        $msg_nombre = 'El nombre es obligatorio.';
        $error = true;
    }

    // Validar apellido
    if (isset($_POST['apellido'])) {
        $apellido = trim($_POST['apellido']);
        if (empty($apellido)) {
            $msg_apellido = 'El apellido es obligatorio.';
            $error = true;
        } elseif (strlen($apellido) < 2) {
            $msg_apellido = 'El apellido debe tener al menos 2 caracteres.';
            $error = true;
        }
    } else {
        $msg_apellido = 'El apellido es obligatorio.';
        $error = true;
    }

    // Validar email
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (empty($email)) {
            $msg_email = 'El email es obligatorio.';
            $error = true;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg_email = 'El formato del email no es válido.';
            $error = true;
        } else {
            // Verificar si el email ya existe (excluyendo el usuario actual)
            try {
                $sql_check = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->execute([$email, $usuario_id]);
                if ($stmt_check->fetch()) {
                    $msg_email = 'Este email ya está registrado por otro usuario.';
                    $error = true;
                }
            } catch(PDOException $e) {
                $msg_email = 'Error al verificar el email.';
                $error = true;
            }
        }
    } else {
        $msg_email = 'El email es obligatorio.';
        $error = true;
    }

    // Validar teléfono (opcional)
    $telefono_input = '';
    $telefono_normalizado = null;
    if (isset($_POST['telefono']) && !empty(trim($_POST['telefono']))) {
        $telefono_input = trim($_POST['telefono']);
        $telefono_sin_formato = preg_replace('/\D+/', '', $telefono_input);

        if ($telefono_sin_formato === '') {
            $msg_telefono = 'Ingresá un número válido.';
            $error = true;
        } elseif (strlen($telefono_sin_formato) < 6 || strlen($telefono_sin_formato) > 11) {
            $msg_telefono = 'El teléfono debe tener entre 6 y 11 dígitos sin el código de país.';
            $error = true;
        }

        if (!$error) {
            $telefono_normalizado = '+54' . $telefono_sin_formato;
            $total_digitos = strlen('54' . $telefono_sin_formato);
            if ($total_digitos < 10 || $total_digitos > 15) {
                $msg_telefono = 'Incluí un teléfono válido (10 a 15 dígitos con el prefijo +54).';
                $error = true;
                $telefono_normalizado = null;
            }
        }
    }

    // Validar dirección (opcional)
    $direccion = '';
    if (isset($_POST['direccion']) && !empty(trim($_POST['direccion']))) {
        $direccion = trim($_POST['direccion']);
    }

    // Procesar foto (opcional)
    $foto_path = null; // absoluta (filesystem) para mover
    $db_foto_path = null; // relativa pública para guardar en BD
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['foto']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $msg_foto = 'Solo se permiten archivos JPG, PNG o GIF.';
            $error = true;
        } elseif ($_FILES['foto']['size'] > 5000000) { // 5MB
            $msg_foto = 'El archivo es demasiado grande. Máximo 5MB.';
            $error = true;
        } else {
            // Crear directorio absoluto de uploads si no existe
            $upload_dir_fs = rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . '/assets/images/usuarios/';
            if (!is_dir($upload_dir_fs)) {
                mkdir($upload_dir_fs, 0777, true);
            }

            // Generar nombre único para la imagen
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $filename = 'usuario_' . $usuario_id . '_' . time() . '.' . $extension;
            $dest_fs = $upload_dir_fs . $filename; // destino en disco
            $db_foto_path = 'assets/images/usuarios/' . $filename; // ruta pública relativa para BD

            // Mover el archivo subido
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $dest_fs)) {
                // Eliminar la foto anterior si existe
                if (!empty($usuario['foto_url'])) {
                    $old = str_replace('\\', '/', $usuario['foto_url']);
                    // Normalizar posibles '../'
                    while (strpos($old, '../') === 0) { $old = substr($old, 3); }
                    if (preg_match('#^https?://#i', $old)) {
                        // no se intenta borrar URLs remotas
                    } else {
                        $oldPublic = ltrim($old, '/');
                        $oldFs = rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . '/' . $oldPublic;
                        if (file_exists($oldFs)) { @unlink($oldFs); }
                    }
                }
            } else {
                $msg_foto = 'Error al guardar la imagen.';
                $error = true;
                $db_foto_path = null;
            }
        }
    }

    // Si no hay errores, actualizar en la base de datos
    if (!$error) {
        try {
            if ($db_foto_path !== null) {
                // Actualizar con nueva foto
                $sql_update = "UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, telefono = ?, direccion = ?, foto_url = ? WHERE id = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $result = $stmt_update->execute([$nombre, $apellido, $email, $telefono_normalizado ?: null, $direccion, $db_foto_path, $usuario_id]);
            } else {
                // Actualizar sin cambiar la foto
                $sql_update = "UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, telefono = ?, direccion = ? WHERE id = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $result = $stmt_update->execute([$nombre, $apellido, $email, $telefono_normalizado ?: null, $direccion, $usuario_id]);
            }

            if ($result) {
                $success_message = 'Perfil actualizado correctamente.';
                // Recargar los datos del usuario
                $sql = "SELECT * FROM usuarios WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$usuario_id]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                $telefono_input = obtenerTelefonoLocal($usuario['telefono'] ?? '');
            } else {
                $error_message = 'Error al actualizar el perfil.';
            }
        } catch(PDOException $e) {
            $error_message = 'Error en la base de datos: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Pet Alert</title>
    <link rel="stylesheet" href="../assets/css/mascota03.css" />
    <link rel="stylesheet" href="../assets/css/perfil-usuario.css" />
    <link rel="stylesheet" href="../assets/css/bottom-nav.css" />
</head>
<body>
    <section class="registro-mascota">
        <div class="formulario">
            <!-- Header -->
            <div class="perfil-header">
                <div class="perfil-title-header">
                    <button onclick="window.location.href='perfil_usuario.php'" class="btn-back-arrow">
                        ← 
                    </button>
                    <h2>Editar Perfil</h2>
                    <div></div> <!-- Spacer para centrar el título -->
                </div>
                
                <!-- Avatar actual -->
                <div class="user-avatar-header">
                    <?php if (!empty($usuario['foto_url']) && file_exists($usuario['foto_url'])): ?>
                        <img src="<?= htmlspecialchars($usuario['foto_url']) ?>" 
                             alt="Foto de perfil" class="avatar-img-header">
                    <?php else: ?>
                        <div class="avatar-placeholder-header">
                            <span>👤</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mensajes -->
            <?php if(!empty($success_message)): ?>
                <div class="success-message">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <?php if(isset($error_message)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de edición -->
            <form method="POST" enctype="multipart/form-data" class="edit-form">
                
                <!-- Nombre -->
                <div class="campo">
                    <label for="nombre">Nombre *</label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" 
                           required>
                    <?php if(!empty($msg_nombre)): ?>
                        <div class="error-text"><?= htmlspecialchars($msg_nombre) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Apellido -->
                <div class="campo">
                    <label for="apellido">Apellido *</label>
                    <input type="text" 
                           id="apellido" 
                           name="apellido" 
                           value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>" 
                           required>
                    <?php if(!empty($msg_apellido)): ?>
                        <div class="error-text"><?= htmlspecialchars($msg_apellido) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="campo">
                    <label for="email">Email *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" 
                           required>
                    <?php if(!empty($msg_email)): ?>
                        <div class="error-text"><?= htmlspecialchars($msg_email) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Teléfono -->
                <div class="campo">
                    <label for="telefono">Teléfono</label>
              <input type="tel" 
                  id="telefono" 
                  name="telefono" 
                  value="<?= htmlspecialchars($telefono_input) ?>" 
                  placeholder="Ingresá el número sin el +54">
              <small class="campo-hint">Se guardará automáticamente con prefijo +54.</small>
                    <?php if(!empty($msg_telefono)): ?>
                        <div class="error-text"><?= htmlspecialchars($msg_telefono) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Dirección -->
                <div class="campo">
                    <label for="direccion">Dirección</label>
                    <input type="text" 
                           id="direccion" 
                           name="direccion" 
                           value="<?= htmlspecialchars($usuario['direccion'] ?? '') ?>" 
                           placeholder="Opcional">
                </div>

                <!-- Foto -->
                <div class="campo">
                    <label for="foto">Cambiar foto de perfil</label>
                    <input type="file" 
                           id="foto" 
                           name="foto" 
                           accept="image/*"
                           onchange="previewImage(this)">
                    <div class="file-info">Solo JPG, PNG o GIF. Máximo 5MB.</div>
                    <?php if(!empty($msg_foto)): ?>
                        <div class="error-text"><?= htmlspecialchars($msg_foto) ?></div>
                    <?php endif; ?>
                    
                    <!-- Preview de la nueva imagen -->
                    <div id="imagePreview" style="display: none;">
                        <img id="preview" alt="Vista previa" style="max-width: 100px; max-height: 100px; border-radius: 15px; margin-top: 10px; border: 2px solid #c9a7f5; object-fit: cover;">
                    </div>
                </div>

                <!-- Botones -->
                <div class="form-buttons">
                    <button type="submit" name="btn_actualizar" class="btn_enviar">
                        Actualizar perfil
                    </button>
                    
                    <button type="button" onclick="window.location.href='perfil_usuario.php'" class="btn-cancel">
                        Cancelar
                    </button>
                </div>
            </form>

        </div>
    </section>

    <?php include __DIR__ . '/../includes/bottom_nav.php'; ?>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>
</body>
</html>
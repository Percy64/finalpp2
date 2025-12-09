<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../usuario/iniciosesion.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id_mascota = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_mascota === 0) {
    header('Location: ../usuario/perfil_usuario.php');
    exit;
}

// Verificar que la mascota pertenece al usuario
$sql_check = "SELECT id_mascota FROM mascotas WHERE id_mascota = ? AND id = ?";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([$id_mascota, $usuario_id]);
if (!$stmt_check->fetch()) {
    header('Location: ../usuario/perfil_usuario.php');
    exit;
}

// Obtener datos actuales de la mascota
$sql = "SELECT * FROM mascotas WHERE id_mascota = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_mascota]);
$mascota = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mascota) {
    header('Location: ../usuario/perfil_usuario.php');
    exit;
}

// Inicializar variables y mensajes
$nombre = $mascota['nombre'];
$especie = $mascota['especie'];
$edad = $mascota['edad'];
$raza = $mascota['raza'];
$color = $mascota['color'];
$sexo = $mascota['sexo'];

$msg_nombre = '';
$msg_especie = '';
$msg_edad = '';
$msg_raza = '';
$msg_color = '';
$msg_foto = '';
$error = false;

if(isset($_POST['btn_actualizar'])){
    
    // Validar nombre
    if(isset($_POST['nombre'])){
        $nombre = trim($_POST['nombre']);
        if(empty($nombre)){
            $msg_nombre = 'El nombre no puede estar vacío';
            $error = true;
        }elseif(strlen($nombre) < 2 || strlen($nombre) > 50){
            $msg_nombre = 'Debe tener entre 2 y 50 caracteres';
            $error = true;
        }
    }

    // Validar especie
    if(isset($_POST['especie'])){
        $especie = trim($_POST['especie']);
        if(empty($especie)){
            $msg_especie = 'Seleccione especie';
            $error = true;
        }
    }

    // Validar edad
    if(isset($_POST['edad'])){
        $edad = trim($_POST['edad']);
        if(empty($edad)){
            $msg_edad = 'Ingrese la edad';
            $error = true;
        }elseif(!is_numeric($edad) || $edad < 0 || $edad > 30){
            $msg_edad = 'La edad debe ser un número entre 0 y 30';
            $error = true;
        }
    }

    // Validar raza (opcional)
    if(isset($_POST['raza'])){
        $raza = trim($_POST['raza']);
        if(strlen($raza) > 100){
            $msg_raza = 'La raza no puede tener más de 100 caracteres';
            $error = true;
        }
    }

    // Validar color (opcional)
    if(isset($_POST['color'])){
        $color = trim($_POST['color']);
        if(strlen($color) > 50){
            $msg_color = 'El color no puede tener más de 50 caracteres';
            $error = true;
        }
    }

    // Validar sexo
    if(isset($_POST['sexo'])){
        $sexo = trim($_POST['sexo']);
    }

    if (!$error) {
        try {
            // Procesar la imagen si se sube una nueva
            $foto_path = $mascota['foto_url'];
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                if(!in_array($_FILES['foto']['type'], $allowed_types)){
                    $msg_foto = 'Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.';
                    $error = true;
                } elseif($_FILES['foto']['size'] > $max_size){
                    $msg_foto = 'La imagen es demasiado grande. Máximo 5MB.';
                    $error = true;
                } else {
                    $upload_dir = '../assets/images/mascotas/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $filename = 'mascota_' . $usuario_id . '_' . time() . '.' . $extension;
                    $new_foto_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $new_foto_path)) {
                        // Eliminar la foto anterior si existe
                        if (!empty($mascota['foto_url']) && file_exists($mascota['foto_url'])) {
                            unlink($mascota['foto_url']);
                        }
                        $foto_path = $new_foto_path;
                    } else {
                        $msg_foto = 'Error al guardar la imagen.';
                        $error = true;
                    }
                }
            }

            if (!$error) {
                $sql_update = "UPDATE mascotas SET nombre = ?, especie = ?, edad = ?, raza = ?, color = ?, sexo = ?, foto_url = ? WHERE id_mascota = ? AND id = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $result = $stmt_update->execute([
                    $nombre, 
                    $especie, 
                    (int)$edad, 
                    !empty($raza) ? $raza : null, 
                    !empty($color) ? $color : null, 
                    $sexo, 
                    $foto_path,
                    $id_mascota,
                    $usuario_id
                ]);
                
                if ($result) {
                    $success_message = "Mascota actualizada exitosamente.";
                    // Recargar datos
                    $stmt->execute([$id_mascota]);
                    $mascota = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $msg_general = 'Error al actualizar la mascota.';
                }
            }
        } catch(PDOException $e) {
            $msg_general = 'Error al actualizar la mascota: ' . $e->getMessage();
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
  <title>Editar Mascota</title>
  <link rel="stylesheet" href="../assets/css/mascota03.css" />
  <link rel="stylesheet" href="../assets/css/registro-mascota-addon.css" />
  <link rel="stylesheet" href="../assets/css/bottom-nav.css" />
</head>
<body>
  <section class="registro-mascota">
    <form class="formulario" method="post" action="" enctype="multipart/form-data">
      <!-- Header con flecha de regreso -->
      <div class="form-header">
        <button type="button" onclick="window.location.href='perfil_mascota.php?id=<?= $id_mascota ?>'" class="btn-back-arrow">
          ← 
        </button>
        <h2>EDITAR MASCOTA</h2>
        <div></div>
      </div>

      <?php if(isset($success_message)): ?>
        <div class="success-message">
          <?= htmlspecialchars($success_message) ?>
        </div>
      <?php endif; ?>

      <?php if(isset($msg_general) && !empty($msg_general)): ?>
        <div class="error-message">
          <?= htmlspecialchars($msg_general) ?>
        </div>
      <?php endif; ?>

      <div class="foto-mascota">
        <?php if (!empty($mascota['foto_url'])): ?>
          <img src="<?= htmlspecialchars($mascota['foto_url']) ?>" alt="Foto actual" id="preview-foto" class="preview-foto" style="display:block;" />
        <?php else: ?>
          <img id="preview-foto" src="" alt="Vista previa" class="preview-foto" style="display:none;" />
        <?php endif; ?>
        
        <input type="file" id="input-foto" name="foto" accept="image/*" class="input-foto-hidden" />
        <button type="button" class="btn-foto" id="btn-foto" onclick="document.getElementById('input-foto').click()">
          📷 Cambiar foto
        </button>
        <?php if(!empty($msg_foto)): ?>
          <output class="msg_foto"><?= $msg_foto ?></output>
        <?php endif; ?>
      </div>

      <input type="text" name="nombre" placeholder="Nombre" value="<?= htmlspecialchars($nombre) ?>" />
      <output class="msg_nombre"><?= $msg_nombre ?></output>

      <select name="especie" required>
        <option value="" disabled>Especie</option>
        <option value="perro" <?= $especie == 'perro' ? 'selected' : '' ?>>Perro</option>
        <option value="gato" <?= $especie == 'gato' ? 'selected' : '' ?>>Gato</option>
      </select>
      <output class="msg_especie"><?= $msg_especie ?></output>

      <input type="number" name="edad" placeholder="Edad (años)" value="<?= htmlspecialchars($edad) ?>" min="0" max="30" />
      <output class="msg_edad"><?= $msg_edad ?></output>

      <input type="text" name="raza" placeholder="Raza (opcional)" value="<?= htmlspecialchars($raza) ?>" />
      <output class="msg_raza"><?= $msg_raza ?></output>

      <input type="text" name="color" placeholder="Color (opcional)" value="<?= htmlspecialchars($color) ?>" />
      <output class="msg_color"><?= $msg_color ?></output>
      
      <select name="sexo" required>
        <option value="" disabled>Sexo</option>
        <option value="macho" <?= $sexo == 'macho' ? 'selected' : '' ?>>Macho</option>
        <option value="hembra" <?= $sexo == 'hembra' ? 'selected' : '' ?>>Hembra</option>
      </select>

      <button type="submit" name="btn_actualizar" class="btn_enviar">Actualizar mascota</button>
    </form>
  </section>

  <?php include __DIR__ . '/../includes/bottom_nav.php'; ?>

  <script>
    const inputFoto = document.getElementById('input-foto');
    const previewFoto = document.getElementById('preview-foto');
    const btnFoto = document.getElementById('btn-foto');

    inputFoto.addEventListener('change', () => {
      const file = inputFoto.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          previewFoto.src = e.target.result;
          previewFoto.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });
  </script>

</body>
</html>

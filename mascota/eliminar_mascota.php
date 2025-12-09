<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
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

// Verificar que la mascota pertenece al usuario y obtener datos necesarios
$sql_check = "SELECT foto_url, id_qr FROM mascotas WHERE id_mascota = ? AND id = ?";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([$id_mascota, $usuario_id]);
$mascota = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$mascota) {
    $_SESSION['error_message'] = 'No tienes permiso para eliminar esta mascota.';
    header('Location: ../usuario/perfil_usuario.php');
    exit;
}

// Confirmar eliminación
if (isset($_POST['confirmar_eliminar'])) {
    try {
    // Eliminar foto física si existe (mapear ruta pública a filesystem)
    if (!empty($mascota['foto_url'])) {
      $publicPath = ltrim(str_replace('\\', '/', $mascota['foto_url']), '/');
      $fsPath = rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . '/' . $publicPath;
      if (file_exists($fsPath)) { @unlink($fsPath); }
    }
        
        // Eliminar historial médico
        $sql_historial = "DELETE FROM historial_medico WHERE id_mascota = ?";
        $stmt_historial = $pdo->prepare($sql_historial);
        $stmt_historial->execute([$id_mascota]);

    // Eliminar mascota (libera FK hacia codigos_qr)
        $sql_delete = "DELETE FROM mascotas WHERE id_mascota = ? AND id = ?";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->execute([$id_mascota, $usuario_id]);

    // Eliminar código QR si estaba asociado
    if (!empty($mascota['id_qr'])) {
      $sql_qr = "DELETE FROM codigos_qr WHERE id_qr = ?";
      $stmt_qr = $pdo->prepare($sql_qr);
      $stmt_qr->execute([$mascota['id_qr']]);
    }
        
        $_SESSION['success_message'] = 'Mascota eliminada correctamente.';
        header('Location: ../usuario/perfil_usuario.php');
        exit;
    } catch(PDOException $e) {
        $error_message = 'Error al eliminar la mascota: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Eliminar Mascota</title>
  <link rel="stylesheet" href="../assets/css/mascota03.css" />
  <link rel="stylesheet" href="../assets/css/bottom-nav.css" />
  <style>
    .delete-confirm {
      max-width: 420px;
      margin: 40px auto;
      text-align: center;
      padding: 20px;
    }
    .warning-icon {
      font-size: 64px;
      margin-bottom: 16px;
    }
    .delete-actions {
      display: flex;
      gap: 12px;
      justify-content: center;
      margin-top: 24px;
    }
    .btn-danger {
      background: #dc3545;
      color: #fff;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
    }
    .btn-cancel {
      background: #6c757d;
      color: #fff;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <section class="registro-mascota">
    <div class="delete-confirm">
      <div class="warning-icon">⚠️</div>
      <h2>¿Estás seguro?</h2>
      <p>Esta acción eliminará permanentemente la mascota y toda su información asociada (historial médico, QR, etc.).</p>
      
      <?php if(isset($error_message)): ?>
        <div class="error-message">
          <?= htmlspecialchars($error_message) ?>
        </div>
      <?php endif; ?>
      
      <form method="post">
        <div class="delete-actions">
          <button type="submit" name="confirmar_eliminar" class="btn-danger">
            Eliminar permanentemente
          </button>
          <button type="button" class="btn-cancel" onclick="window.location.href='perfil_mascota.php?id=<?= $id_mascota ?>'">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </section>

  <?php include __DIR__ . '/../includes/bottom_nav.php'; ?>
</body>
</html>

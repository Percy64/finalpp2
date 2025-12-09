<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php';

$idMascota = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mascota = null;

if ($idMascota > 0) {
    try {
        $sql = "SELECT m.*, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario, u.telefono, u.email, u.direccion
                FROM mascotas m
                LEFT JOIN usuarios u ON m.id = u.id
                WHERE m.id_mascota = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idMascota]);
        $mascota = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('QR info error: ' . $e->getMessage());
    }
}

function obtenerFotoQr(array $mascota): string
{
    $rutaOriginal = isset($mascota['foto_url']) ? trim((string) $mascota['foto_url']) : '';

    if ($rutaOriginal === '') {
        return ASSETS_URL . '/images/dog-placeholder.svg';
    }

    // Si ya es una URL absoluta, devolverla directamente
    if (preg_match('#^https?://#i', $rutaOriginal)) {
        return $rutaOriginal;
    }

    $rutaNormalizada = str_replace('\\', '/', $rutaOriginal);
    $rutaNormalizada = preg_replace('#^\./#', '', $rutaNormalizada);

    // Eliminar recorridos ../ iniciales para mapear al root del proyecto
    while (strpos($rutaNormalizada, '../') === 0) {
        $rutaNormalizada = substr($rutaNormalizada, 3);
    }

    $publicPath = ltrim($rutaNormalizada, '/');
    $basePrefijo = ltrim(BASE_URL, '/');
    if ($basePrefijo !== '' && strpos($publicPath, $basePrefijo . '/') === 0) {
        $publicPath = substr($publicPath, strlen($basePrefijo) + 1);
    }

    $localPath = ROOT_PATH . '/' . $publicPath;

    // Si no existe aún, intentar buscar dentro de assets/images/mascotas conservando el nombre de archivo
    if (!file_exists($localPath) && strpos($publicPath, 'assets/') !== 0) {
        $basename = basename($publicPath);
        $alternative = 'assets/images/mascotas/' . $basename;
        $alternativePath = ROOT_PATH . '/' . $alternative;
        if (file_exists($alternativePath)) {
            $publicPath = $alternative;
            $localPath = $alternativePath;
        }
    }

    if (file_exists($localPath)) {
        return rtrim(BASE_URL, '/') . '/' . $publicPath;
    }

    return ASSETS_URL . '/images/dog-placeholder.svg';
}

$estado = 'normal';
$estadoOriginal = null;
$descripcionEstado = '';
$fechaEvento = '';
$perdidoFlag = 0;

if ($mascota) {
    $estadoOriginal = isset($mascota['estado']) ? trim((string)$mascota['estado']) : null;
    if ($estadoOriginal !== null && $estadoOriginal !== '') {
        $estado = $estadoOriginal;
    }

    if (isset($mascota['perdido'])) {
        $perdidoFlag = is_numeric($mascota['perdido']) ? (int)$mascota['perdido'] : (strtolower((string)$mascota['perdido']) === 'si' ? 1 : 0);
    }

    if ($estado === 'normal' && $perdidoFlag === 1) {
        $estado = 'perdida';
    }

    $descripcionEstado = $mascota['descripcion_estado'] ?? '';
    $fechaEvento = $mascota['fecha_estado'] ?? $mascota['fecha_registro'] ?? '';
}

$esPerdida = ($estado === 'perdida');
$nombreResponsable = $mascota ? trim(($mascota['nombre_usuario'] ?? '') . ' ' . ($mascota['apellido_usuario'] ?? '')) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de la mascota</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/nousuario.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css">
    <style>
      .status-pill {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:6px 14px;
        border-radius:20px;
        font-weight:600;
        font-size:0.9rem;
        margin-top:4px;
      }
      .status-normal { background:#e3f2fd; color:#1976d2; }
      .status-perdida { background:#ffebee; color:#c62828; }
      .status-encontrada { background:#e8f5e9; color:#2e7d32; }
      .info-list {
        list-style:none;
        padding:0;
        margin:18px 0 0;
        border-top:1px solid rgba(0,0,0,0.08);
      }
      .info-list li {
        display:flex;
        justify-content:space-between;
        padding:12px 0;
        border-bottom:1px solid rgba(0,0,0,0.05);
        font-size:15px;
        color:#333;
      }
      .info-list span:first-child { font-weight:600; color:#555; }
            .buttons-container { margin-top:24px; display:flex; flex-direction:column; gap:12px; }
      .btn-secondary { background:#888; color:#fff; border:none; padding:14px; border-radius:50px; font-weight:600; display:flex; justify-content:center; align-items:center; gap:8px; }
      .btn-ghost { background:#fff; color:#6b4fd2; border:2px solid #6b4fd2; }
      .alert-box {
        text-align:center;
        background:#fff;
        padding:30px;
        border-radius:20px;
        box-shadow:0 4px 20px rgba(0,0,0,0.08);
        max-width:420px;
        margin:40px auto;
      }
      .alert-box p { color:#555; }
            .contact-summary {
                margin-top:18px;
                background:#f3ecff;
                border-radius:18px;
                padding:16px;
                color:#4a358c;
                font-size:14px;
                box-shadow:0 4px 14px rgba(107, 79, 210, 0.18);
            }
            .contact-summary strong { display:block; font-size:16px; }
    </style>
    </head>
<body>
    <div class="container">
        <div class="content">
            <?php if ($mascota): ?>
                <div class="image-container" style="height:300px;">
                    <img src="<?= htmlspecialchars(obtenerFotoQr($mascota)) ?>" alt="<?= htmlspecialchars($mascota['nombre']) ?>" />
                </div>

                <div class="header">
                    <h1 class="title"><?= htmlspecialchars($mascota['nombre']) ?></h1>
                    <div>
                        <?php
                            $claseStatus = 'status-normal';
                            $textoStatus = 'Estado desconocido';
                            switch ($estado) {
                                case 'perdida':
                                    $claseStatus = 'status-perdida';
                                    $textoStatus = 'Mascota reportada como perdida';
                                    break;
                                case 'encontrada':
                                    $claseStatus = 'status-encontrada';
                                    $textoStatus = 'Mascota encontrada / en resguardo';
                                    break;
                                default:
                                    $claseStatus = 'status-normal';
                                    $textoStatus = 'Mascota registrada';
                            }
                        ?>
                        <div class="status-pill <?= $claseStatus ?>"><?= $textoStatus ?></div>
                    </div>
                </div>

                <p class="description">
                    <?php if ($descripcionEstado): ?>
                        <?= htmlspecialchars($descripcionEstado) ?>
                    <?php elseif ($esPerdida): ?>
                        <?= htmlspecialchars('Esta mascota fue reportada como perdida. Si la viste, avisá a su responsable lo antes posible.') ?>
                    <?php else: ?>
                        <?= htmlspecialchars('Esta mascota está registrada en la plataforma Pet Alert. Guardá este enlace para confirmar su identidad si fuese necesario.') ?>
                    <?php endif; ?>
                </p>

                <?php if ($esPerdida && $nombreResponsable): ?>
                    <div class="contact-summary">
                        <strong>¡Ayudá a <?= htmlspecialchars($mascota['nombre']) ?> a volver a casa!</strong>
                        Contactá a <?= htmlspecialchars($nombreResponsable) ?> si tenés novedades o viste a la mascota.
                    </div>
                <?php endif; ?>

                <ul class="info-list">
                    <li><span>Especie</span><span><?= htmlspecialchars(ucfirst($mascota['especie'] ?? '')) ?></span></li>
                    <?php if (!empty($mascota['color'])): ?><li><span>Color</span><span><?= htmlspecialchars($mascota['color']) ?></span></li><?php endif; ?>
                    <?php if (!empty($mascota['raza'])): ?><li><span>Raza</span><span><?= htmlspecialchars($mascota['raza']) ?></span></li><?php endif; ?>
                    <li><span>Edad</span><span><?= isset($mascota['edad']) ? htmlspecialchars($mascota['edad']) . ' años' : 'No informada' ?></span></li>
                    <?php if ($nombreResponsable): ?><li><span>Responsable</span><span><?= htmlspecialchars($nombreResponsable) ?></span></li><?php endif; ?>
                    <?php if (!empty($mascota['direccion'])): ?><li><span>Zona</span><span><?= htmlspecialchars($mascota['direccion']) ?></span></li><?php endif; ?>
                    <?php if ($fechaEvento): ?><li><span>Última actualización</span><span><?= date('d/m/Y H:i', strtotime($fechaEvento)) ?></span></li><?php endif; ?>
                </ul>

                <div class="buttons-container">
                    <?php if (!empty($mascota['telefono'])): ?>
                        <a href="tel:<?= htmlspecialchars($mascota['telefono']) ?>" class="btn">
                            📞 Llamar a su responsable
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($mascota['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($mascota['email']) ?>" class="btn-secondary">
                            ✉️ Enviar correo
                        </a>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars(BASE_URL . '/home/index.php') ?>" class="btn-secondary btn-ghost">
                        🏠 Ir a la página principal
                    </a>
                    
                </div>
                <p style="margin-top:18px; font-size:13px; color:#777; text-align:center;">¿Sos el dueño? Iniciá sesión para editar la información.</p>
            <?php else: ?>
                <div class="alert-box">
                    <div style="font-size:48px;">🐾</div>
                    <h2>Mascota no encontrada</h2>
                    <p>No pudimos cargar la información. Verificá el código QR o volvé a intentarlo.</p>
                    <div class="buttons-container">
                        <a href="<?= BASE_URL ?>/home/index.php" class="btn-secondary btn-ghost">Volver al inicio</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
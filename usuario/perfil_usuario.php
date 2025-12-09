<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: iniciosesion.php');
    exit;
}

// Obtener datos del usuario
$usuario_id = $_SESSION['usuario_id'];
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
} catch(PDOException $e) {
    $error_message = 'Error al cargar el perfil.';
}

// Obtener mascotas del usuario
$mascotas = [];
$debug_sql = '';
try {
    // La consulta está correcta: usar 'id' que es la FK en la tabla mascotas que referencia usuarios.id
    $sql_mascotas = "SELECT * FROM mascotas WHERE id = ? ORDER BY fecha_registro DESC";
    $debug_sql = "SQL: " . $sql_mascotas . " con parametro: " . $usuario_id;
    $stmt_mascotas = $pdo->prepare($sql_mascotas);
    $stmt_mascotas->execute([$usuario_id]);
    $mascotas = $stmt_mascotas->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $mascotas = [];
    $debug_error = "Error al obtener mascotas: " . $e->getMessage();
    error_log($debug_error);
    // Mostrar error temporal para debug
    if (isset($_GET['debug'])) {
        echo "<div style='color: red; margin: 10px; padding: 10px; border: 1px solid red;'>";
        echo "Error SQL: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "SQL ejecutado: " . htmlspecialchars($sql_mascotas) . "<br>";
        echo "Parámetro: " . $usuario_id;
        echo "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Pet Alert</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/perfil-usuario.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css" />
</head>
<body>
    <?php
    // Helpers para normalizar y mostrar imágenes desde rutas relativas guardadas en BD
    function urlPublicaGenerica(?string $ruta, string $fallback): string {
        if (!$ruta || trim($ruta) === '') return $fallback;
        // Si ya es URL absoluta
        if (preg_match('#^https?://#i', $ruta)) return $ruta;
        // Normalizar y construir URL pública
        $publicPath = ltrim(str_replace('\\', '/', $ruta), '/');
        $fullUrl = rtrim(BASE_URL, '/') . '/' . $publicPath;
        // Verificar existencia en filesystem; si no existe, usar fallback
        $localPath = rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . '/' . $publicPath;
        if (!file_exists($localPath)) return $fallback;
        return $fullUrl;
    }

    function urlPublicaMascota(?string $ruta, ?string $especie): string {
        $base = ASSETS_URL . '/images';
        $fallback = $base . '/dog-placeholder.svg';
        if ($especie) {
            $e = strtolower($especie);
            if ($e === 'gato') $fallback = $base . '/cat-placeholder.svg';
            if ($e === 'perro') $fallback = $base . '/dog-placeholder.svg';
        }
        return urlPublicaGenerica($ruta, $fallback);
    }
    ?>
    <section class="registro-mascota">
        <div class="formulario">
            <!-- Header con navegación -->
            <div class="perfil-header">
                <div class="perfil-title-header">
                    <h2>Mi Perfil</h2>
                </div>
                
                <!-- Avatar centrado debajo del título -->
                <div class="user-avatar-header">
                    <?php 
                        $avatarUrl = urlPublicaGenerica($usuario['foto_url'] ?? null, ASSETS_URL . '/images/usuarios/default-avatar.png');
                    ?>
                    <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Foto de perfil" class="avatar-img-header"
                         onerror="this.src='<?= ASSETS_URL ?>/images/usuarios/default-avatar.png'">
                </div>
            </div>

            <?php if(isset($error_message)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <!-- Información del usuario -->
            <div class="user-info">
                <!-- Detalles del usuario -->
                <div class="user-details-center">
                    <h3><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h3>
                    <p class="user-email"><?= htmlspecialchars($usuario['email']) ?></p>
                    <?php if (!empty($usuario['telefono'])): ?>
                        <p class="user-phone">📞 <?= htmlspecialchars($usuario['telefono']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($usuario['direccion'])): ?>
                        <p class="user-address">📍 <?= htmlspecialchars($usuario['direccion']) ?></p>
                    <?php endif; ?>
                    <p class="user-since">Miembro desde: <?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></p>
                    
                    <!-- Botón de editar perfil -->
                    <div class="edit-profile-section">
                        <button onclick="window.location.href='editar_perfil.php'" class="btn-edit-profile">
                            ✏️ Editar perfil
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sección de mascotas -->
            <div class="mascotas-section">
                <div class="section-header">
                    <h3>Mis Mascotas</h3>
                    <button onclick="window.location.href='../mascota/registro_mascota.php'" class="btn-add-pet">➕ Agregar</button>
                </div>

                <!-- Debug temporal -->
                <?php if (isset($_GET['debug'])): ?>
                    <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px; font-size: 12px;">
                        <strong>Debug Info:</strong><br>
                        Usuario ID: <?= $usuario_id ?><br>
                        SQL: <?= htmlspecialchars($debug_sql) ?><br>
                        Número de mascotas encontradas: <?= count($mascotas) ?><br>
                        <?php if (!empty($mascotas)): ?>
                            Mascotas: 
                            <ul>
                                <?php foreach($mascotas as $m): ?>
                                    <li><?= htmlspecialchars($m['nombre']) ?> (ID: <?= $m['id_mascota'] ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($mascotas)): ?>
                    <div class="no-pets">
                        <p>No tienes mascotas registradas aún.</p>
                        <button onclick="window.location.href='../mascota/registro_mascota.php'" class="btn_enviar">
                            Registrar mi primera mascota
                        </button>
                    </div>
                <?php else: ?>
                    <div class="pets-grid">
                        <?php foreach ($mascotas as $mascota): ?>
                            <?php 
                                $imgUrl = urlPublicaMascota($mascota['foto_url'] ?? null, $mascota['especie'] ?? null);
                            ?>
                            <div class="pet-card-profile">
                                <div class="pet-image-container">
                                    <img src="<?= htmlspecialchars($imgUrl) ?>" 
                                         alt="<?= htmlspecialchars($mascota['nombre']) ?>" 
                                         class="pet-image-profile"
                                         onerror="this.src='<?= ASSETS_URL ?>/images/<?= ($mascota['especie'] ?? '') === 'gato' ? 'cat' : 'dog' ?>-placeholder.svg'">
                                </div>
                                
                                <div class="pet-info">
                                    <h4><?= htmlspecialchars($mascota['nombre']) ?></h4>
                                    <p><?= ucfirst(htmlspecialchars($mascota['especie'])) ?></p>
                                    <?php if (!empty($mascota['raza'])): ?>
                                        <p class="pet-breed"><?= htmlspecialchars($mascota['raza']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($mascota['edad'])): ?>
                                        <p class="pet-age">🎂 <?= htmlspecialchars($mascota['edad']) ?> años</p>
                                    <?php endif; ?>
                                    <?php if (!empty($mascota['color'])): ?>
                                        <p class="pet-color">🎨 <?= htmlspecialchars($mascota['color']) ?></p>
                                    <?php endif; ?>
                                    <div class="pet-actions">
                                        <button onclick="window.location.href='../mascota/perfil_mascota.php?id=<?= $mascota['id_mascota'] ?>'" 
                                                class="btn-view">Ver</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Botón de cerrar sesión al final -->
            <div class="logout-section">
                <button onclick="cerrarSesion()" class="btn-logout-bottom">
                    🚪 Cerrar sesión
                </button>
            </div>

        </div>
    </section>

    <!-- Barra de navegación inferior compartida -->
    <?php include __DIR__ . '/../includes/bottom_nav.php'; ?>

    <script>
        function cerrarSesion() {
            if(confirm('¿Estás seguro que deseas cerrar sesión?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
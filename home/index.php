<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php'; // PDO $pdo

$usuario_logueado = isset($_SESSION['usuario_id']);

// Obtener mascotas perdidas desde la base de datos (estado = 'perdida')
$mascotas = [];
// Métricas para portada
$estadisticas = [
    'reunidas_mes' => 0,
    'usuarios_activos' => 0,
];
try {
    // Consulta avanzada: incluye última ubicación de mascotas_perdidas (si la tabla existe)
    $sql = "SELECT
                m.id_mascota,
                m.nombre,
                m.especie,
                m.raza,
                m.color,
                m.foto_url,
                m.estado,
                m.descripcion_estado,
                m.fecha_estado,
                COALESCE(mp.direccion, u.direccion) AS ubicacion,
                mp.latitud,
                mp.longitud,
                mp.fecha_perdida
            FROM mascotas m
            LEFT JOIN (
                SELECT mp1.*
                FROM mascotas_perdidas mp1
                JOIN (
                    SELECT id_mascota, MAX(id_perdida) AS max_id
                    FROM mascotas_perdidas
                    GROUP BY id_mascota
                ) last ON last.id_mascota = mp1.id_mascota AND last.max_id = mp1.id_perdida
            ) mp ON mp.id_mascota = m.id_mascota
            LEFT JOIN usuarios u ON m.id = u.id
            WHERE TRIM(m.estado) = 'perdida' OR mp.id_perdida IS NOT NULL
            ORDER BY COALESCE(m.fecha_estado, mp.fecha_perdida) DESC, m.id_mascota DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si falla (p.ej., tabla mascotas_perdidas no existe), usar fallback simple por estado
    try {
        $sqlSimple = "SELECT m.id_mascota, m.nombre, m.especie, m.raza, m.color, m.foto_url, m.estado, m.descripcion_estado, m.fecha_estado,
                             u.direccion AS ubicacion, NULL AS latitud, NULL AS longitud, NULL AS fecha_perdida
                      FROM mascotas m
                      LEFT JOIN usuarios u ON m.id = u.id
                      WHERE TRIM(m.estado) = 'perdida'
                      ORDER BY m.fecha_estado DESC, m.id_mascota DESC";
        $stmt2 = $pdo->prepare($sqlSimple);
        $stmt2->execute();
        $mascotas = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e2) {
        $mascotas = [];
    }
}

// Cargar estadísticas básicas (seguras y rápidas)
try {
    // Mascotas reunidas este mes: estado encontrada con fecha_estado en mes/año actual
    $sqlReunidas = "SELECT COUNT(*) AS c FROM mascotas
                    WHERE estado = 'encontrada'
                      AND fecha_estado IS NOT NULL
                      AND YEAR(fecha_estado) = YEAR(CURRENT_DATE())
                      AND MONTH(fecha_estado) = MONTH(CURRENT_DATE())";
    $estadisticas['reunidas_mes'] = (int)$pdo->query($sqlReunidas)->fetchColumn();

    // Usuarios activos (placeholder: total usuarios)
    $estadisticas['usuarios_activos'] = (int)$pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
} catch (PDOException $e) {
    // fallback silencioso
}

// Función helper para determinar imagen placeholder según especie
function imagenPlaceholder(?string $especie): string {
    $base = ASSETS_URL . '/images';
    if (!$especie) return $base . '/dog-placeholder.svg';
    $especie = strtolower($especie);
    if ($especie === 'gato') return $base . '/cat-placeholder.svg';
    if ($especie === 'perro') return $base . '/dog-placeholder.svg';
    return $base . '/rabbit-placeholder.svg';
}

// Formatea ubicación similar a mapa.php: dirección si hay; si no, lat,lng con 5 decimales; fallback
function formatoUbicacionLista(?string $direccion, $latitud, $longitud): string {
    $direccion = trim((string)$direccion);
    if ($direccion !== '') return $direccion;
    if ($latitud !== null && $longitud !== null && $latitud !== '' && $longitud !== '') {
        $lat = number_format((float)$latitud, 5, '.', '');
        $lng = number_format((float)$longitud, 5, '.', '');
        return $lat . ', ' . $lng;
    }
    return 'Ubicación no especificada';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mascotas Perdidas</title>
        <?php 
            $homeCssV = @filemtime(ROOT_PATH . '/assets/css/home.css');
            $bottomNavCssV = @filemtime(ROOT_PATH . '/assets/css/bottom-nav.css');
        ?>
        <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/home.css?v=<?= $homeCssV ?>">
        <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css?v=<?= $bottomNavCssV ?>">
</head>
<body>

    <div class="content-wrapper">
        <!-- Encabezado -->
        <header class="hero">
            <h1>Mascotas Perdidas</h1>
            <p>Ayudando a reunir familias con sus mascotas</p>
        </header>

        <!-- Estadísticas resumidas -->
        <section class="stats">
            <div class="stat-card">
                <strong><?= (int)$estadisticas['reunidas_mes'] ?></strong>
                <span>Mascotas reunidas este mes</span>
            </div>
            <div class="stat-card">
                <strong><?= (int)$estadisticas['usuarios_activos'] ?></strong>
                <span>Usuarios activos</span>
            </div>
            <div class="stat-card">
                <strong>24/7</strong>
                <span>Soporte disponible</span>
            </div>
        </section>

        

        <section class="novedades">
            <h2>Novedades</h2>
            <p>Ahora podés añadir una mascota encontrada como reporte en la sección de mascotas perdidas de forma anónima. Cuando la mascota sea reclamada por su familia, nos comunicaremos con sus propietarios publicados.</p>
            <?php if (empty($mascotas)): ?>
                <p>No hay mascotas marcadas como perdidas actualmente. ¿Viste una? Reportala para ayudar a su familia.</p>
            <?php endif; ?>
        </section>

        <div class="section-head">
            <h2>Mascotas perdidas recientemente</h2>
            <p><?= count($mascotas) ?> publicadas</p>
        </div>

        <section class="mascotas">
        <div class="mascotas-grid" aria-label="Listado de mascotas perdidas">
            <?php if (empty($mascotas)): ?>
                <!-- Estado vacío (opcionalmente podríamos mostrar una ilustración) -->
            <?php else: ?>
                <?php foreach ($mascotas as $mascota): ?>
                    <?php
                        $descripcion = $mascota['descripcion_estado'] ?: 'Mascota perdida';
                        $ubicacion = formatoUbicacionLista($mascota['ubicacion'] ?? null, $mascota['latitud'] ?? null, $mascota['longitud'] ?? null);
                        $foto = $mascota['foto_url'] ?? '';
                        if ($foto && !preg_match('/^https?:\/\//', $foto)) {
                            $foto_normalizada = preg_replace('#^\.\./#', '', $foto);
                            $foto = BASE_URL . '/' . ltrim($foto_normalizada, '/');
                        }
                        $ruta_relativa = preg_replace('#^' . preg_quote(BASE_URL, '/') . '/#', '', $foto);
                        if (!$foto || !file_exists(__DIR__ . '/../' . ltrim($ruta_relativa, '/'))) {
                            $foto = imagenPlaceholder($mascota['especie'] ?? null);
                        }
                        $chips = [];
                        if (!empty($mascota['especie'])) $chips[] = ucfirst($mascota['especie']);
                        if (!empty($mascota['raza'])) $chips[] = $mascota['raza'];
                        if (!empty($mascota['color'])) $chips[] = $mascota['color'];
                        $fechaEvento = !empty($mascota['fecha_estado']) ? $mascota['fecha_estado'] : ($mascota['fecha_perdida'] ?? null);
                        $fechaLegible = $fechaEvento ? date('d/m H:i', strtotime($fechaEvento)) : '';
                    ?>
                    <a class="pet-card" href="<?= htmlspecialchars(BASE_URL . '/mascota/qrinfo.php?id=' . $mascota['id_mascota']) ?>" style="text-decoration:none; color:inherit; display:block;">
                        <div class="pet-image-container">
                            <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars('Mascota perdida: ' . ($mascota['nombre'] ?? '')) ?>" class="pet-image top-centered" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="image-placeholder" style="display:none; position:absolute; inset:0; background-color:#f0f0f0; align-items:center; justify-content:center; color:#666; font-size:16px;">
                                📷 Imagen no disponible
                            </div>
                        </div>
                        <div class="pet-header">
                            <p class="pet-name"><?= htmlspecialchars($mascota['nombre'] ?? 'Mascota') ?></p>
                            <p class="pet-description"><?= htmlspecialchars($descripcion) ?></p>
                            <div class="pet-location">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span><?= htmlspecialchars($ubicacion) ?><?= $fechaLegible ? ' • ' . htmlspecialchars($fechaLegible) : '' ?></span>
                            </div>
                            <?php if (!empty($chips)): ?>
                                <div class="chips">
                                    <?php foreach ($chips as $chip): ?>
                                        <span class="chip"><?= htmlspecialchars($chip) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pet-footer"></div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        </section>

        <!-- Acciones -->
        <div class="actions-footer">
            <button class="btn-reportar" onclick="window.location.href='<?= BASE_URL ?>/mascota/registro_mascota.php'">Reportar Mascota</button>
        </div>

        <!-- Pie de página -->
        <footer class="site-footer" role="contentinfo">
            <div class="site-footer__inner">
                <p class="site-footer__brand">Lost &amp; Found</p>
                <p class="site-footer__copy">&copy; <?= date('Y') ?> Todos los derechos reservados.</p>
                <nav class="site-footer__links" aria-label="Enlaces legales">
                    <a href="<?= BASE_URL ?>/legal/mision_vision.php">Misión y visión</a>
                    <span aria-hidden="true">·</span>
                    <a href="<?= BASE_URL ?>/legal/terminos-condiciones.html">Términos y condiciones</a>
                </nav>
            </div>
        </footer>
    </div>

    <!-- Barra de navegación inferior compartida -->
    <?php include __DIR__ . '/../includes/bottom_nav.php'; ?>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/config.php';

// Obtener ID de la mascota desde GET
$id_mascota = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Consulta para obtener datos de la mascota y su dueño
$sql = "SELECT m.*, u.nombre, u.apellido, u.telefono, u.email, u.direccion
        FROM mascotas m 
        LEFT JOIN usuarios u ON m.id = u.id 
        WHERE m.id_mascota = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_mascota]);
$mascota = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra la mascota, usar datos de ejemplo
if (!$mascota) {
    $mascota = [
        'id_mascota' => 1,
        'nombre' => 'Firulais',
        'especie' => 'Perro',
        'raza' => 'Labrador',
        'edad' => 3,
        'sexo' => 'Macho',
        'color' => 'Marrón',
        'foto_url' => null,
        'nombre_dueño' => 'Juan',
        'apellido' => 'Pérez',
        'telefono' => '341-5551234',
        'email' => 'juan.perez@example.com',
        'direccion' => 'Calle Falsa 123, Rosario'
    ];
} else {
    // Combinar nombre y apellido del dueño
    $mascota['nombre_dueño'] = trim($mascota['nombre'] . ' ' . $mascota['apellido']);
}

// Helper: construir URL pública de la foto con fallback
function urlPublicaFoto(?string $fotoUrl): string {
    if (!$fotoUrl || trim($fotoUrl) === '') {
        return ASSETS_URL . '/images/dog-placeholder.svg';
    }
    // Si ya es absoluta
    if (preg_match('#^https?://#i', $fotoUrl)) {
        return $fotoUrl;
    }
    // Normalizar y prefijar BASE_URL
    $publicPath = ltrim(str_replace('\\', '/', $fotoUrl), '/');
    $fullUrl = rtrim(BASE_URL, '/') . '/' . $publicPath;

    // Verificar existencia en filesystem para fallback
    $localPath = rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . '/' . $publicPath;
    if (!file_exists($localPath)) {
        return ASSETS_URL . '/images/dog-placeholder.svg';
    }
    return $fullUrl;
}

// Obtener historial médico
$sql_historial = "SELECT * FROM historial_medico WHERE id_mascota = ? ORDER BY fecha DESC LIMIT 3";
$stmt_historial = $pdo->prepare($sql_historial);
$stmt_historial->execute([$id_mascota]);
$historial = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);
// URL pública amigable para el código QR: usar la ruta de información simplificada
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$port = isset($_SERVER['SERVER_PORT']) ? (string)$_SERVER['SERVER_PORT'] : '80';
$portPart = ($scheme === 'http' && $port === '80') || ($scheme === 'https' && $port === '443') ? '' : ':' . $port;
$pet_url = $scheme . '://' . PUBLIC_HOST . $portPart . BASE_URL . '/qr/' . urlencode($mascota['id_mascota']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= htmlspecialchars($mascota['nombre']) ?></title>
    <link rel="stylesheet" href="../assets/css/mascota03.css" />
    <link rel="stylesheet" href="../assets/css/bottom-nav.css" />
</head>
<body>
    <section class="registro-mascota">
        <div class="perfil-mascota">
            <h1 class="titulo-perfil">Perfil Mascota</h1>
            
            <!-- Foto de la mascota -->
            <div class="foto-perfil">
                <?php if (!empty($mascota['foto_url'])): ?>
                    <img src="<?= htmlspecialchars(urlPublicaFoto($mascota['foto_url'])) ?>" 
                         alt="Foto de <?= htmlspecialchars($mascota['nombre']) ?>" 
                         class="imagen-mascota" />
                <?php else: ?>
                    <div class="placeholder-foto">
                        <span>📷</span>
                        <p>Sin foto</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Nombre de la mascota -->
            <h2 class="nombre-mascota"><?= htmlspecialchars($mascota['nombre']) ?></h2>
            
            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $mascota['id']): ?>
            <!-- Estado de la mascota (solo para el dueño) -->
            <div class="estado-mascota" style="text-align:center; margin: 12px 0;">
                <?php 
                $estado_actual = isset($mascota['estado']) ? $mascota['estado'] : 'normal';
                $clase_estado = '';
                $texto_estado = '';
                switch($estado_actual) {
                    case 'perdida':
                        $clase_estado = 'estado-perdida';
                        $texto_estado = '🚨 Mascota Perdida';
                        break;
                    case 'encontrada':
                        $clase_estado = 'estado-encontrada';
                        $texto_estado = '✅ Mascota Encontrada';
                        break;
                    default:
                        $clase_estado = 'estado-normal';
                        $texto_estado = '✨ Estado Normal';
                }
                ?>
                <div class="<?= $clase_estado ?>" style="display:inline-block; padding:8px 16px; border-radius:20px; font-weight:600; margin-bottom:8px;">
                    <?= $texto_estado ?>
                </div>
                <div>
                    <button type="button" class="btn-estado" onclick="toggleEstadoForm()" style="background:#6b4fd2; color:#fff; border:none; padding:8px 14px; border-radius:8px; font-size:14px; cursor:pointer;">
                        Cambiar estado
                    </button>
                </div>
                
                <!-- Formulario para cambiar estado -->
                <div id="estado-form" style="display:none; margin-top:12px; background:#f9f9f9; padding:14px; border-radius:12px;">
                    <select id="nuevo-estado" style="width:100%; padding:8px; border-radius:8px; margin-bottom:8px;">
                        <option value="normal" <?= $estado_actual === 'normal' ? 'selected' : '' ?>>Normal</option>
                        <option value="perdida" <?= $estado_actual === 'perdida' ? 'selected' : '' ?>>Perdida</option>
                        <option value="encontrada" <?= $estado_actual === 'encontrada' ? 'selected' : '' ?>>Encontrada</option>
                    </select>
                    <textarea id="descripcion-estado" placeholder="Descripción (opcional)" style="width:100%; padding:8px; border-radius:8px; min-height:60px; margin-bottom:8px;"><?= htmlspecialchars($mascota['descripcion_estado'] ?? '') ?></textarea>

                    <!-- Campos de ubicación (solo si estado = perdida) -->
                    <div id="ubicacion-perdida" style="display:none; margin-bottom:8px;">
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input id="direccion-perdida" type="text" placeholder="Dirección donde se perdió (opcional)" style="flex:1; padding:8px; border-radius:8px; border:1px solid #ddd;" />
                        </div>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input id="referencia-perdida" type="text" placeholder="Referencia (punto de interés, esquina, etc.)" style="flex:1; padding:8px; border-radius:8px; border:1px solid #ddd;" />
                        </div>
                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input id="latitud-perdida" type="number" step="any" placeholder="Latitud" style="flex:1; padding:8px; border-radius:8px; border:1px solid #ddd;" />
                            <input id="longitud-perdida" type="number" step="any" placeholder="Longitud" style="flex:1; padding:8px; border-radius:8px; border:1px solid #ddd;" />
                        </div>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <input id="fecha-perdida" type="datetime-local" style="flex:1; padding:8px; border-radius:8px; border:1px solid #ddd;" />
                            <button type="button" id="btn-geo" style="background:#6b4fd2; color:#fff; border:none; padding:8px 12px; border-radius:8px; cursor:pointer;">Usar mi ubicación</button>
                        </div>
                        <small id="geo-status" style="display:block; color:#666; margin-top:6px;">Consejo: con "Usar mi ubicación" se completan latitud y longitud automáticamente.</small>
                    </div>
                    <button type="button" onclick="guardarEstado()" style="background:#28a745; color:#fff; border:none; padding:8px 14px; border-radius:8px; margin-right:6px; cursor:pointer;">Guardar</button>
                    <button type="button" onclick="toggleEstadoForm()" style="background:#6c757d; color:#fff; border:none; padding:8px 14px; border-radius:8px; cursor:pointer;">Cancelar</button>
                </div>
            </div>
            <style>
                .estado-normal { background:#e3f2fd; color:#1976d2; }
                .estado-perdida { background:#ffebee; color:#c62828; }
                .estado-encontrada { background:#e8f5e9; color:#2e7d32; }
            </style>
            <?php endif; ?>

            <!-- Información básica de la mascota -->
            <div class="info-mascota">
                <h3>Información de la Mascota</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Especie:</span>
                        <span class="info-value"><?= ucfirst(htmlspecialchars($mascota['especie'])) ?></span>
                    </div>
                    <?php if ($mascota['raza']): ?>
                    <div class="info-item">
                        <span class="info-label">Raza:</span>
                        <span class="info-value"><?= htmlspecialchars($mascota['raza']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <span class="info-label">Sexo:</span>
                        <span class="info-value"><?= ucfirst(htmlspecialchars($mascota['sexo'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Edad:</span>
                        <span class="info-value"><?= htmlspecialchars($mascota['edad']) ?> año<?= $mascota['edad'] != 1 ? 's' : '' ?></span>
                    </div>
                    <?php if ($mascota['color']): ?>
                    <div class="info-item">
                        <span class="info-label">Color:</span>
                        <span class="info-value"><?= htmlspecialchars($mascota['color']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información del dueño -->
            <div class="info-dueño">
                <h3>Información del Dueño</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value"><?= htmlspecialchars($mascota['nombre_dueño'] ?? 'No disponible') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Teléfono:</span>
                        <span class="info-value">
                            <?php if ($mascota['telefono']): ?>
                                <a href="tel:<?= htmlspecialchars($mascota['telefono']) ?>">
                                    <?= htmlspecialchars($mascota['telefono']) ?>
                                </a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if ($mascota['email']): ?>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">
                            <a href="mailto:<?= htmlspecialchars($mascota['email']) ?>">
                                <?= htmlspecialchars($mascota['email']) ?>
                            </a>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if ($mascota['direccion']): ?>
                    <div class="info-item">
                        <span class="info-label">Dirección:</span>
                        <span class="info-value"><?= htmlspecialchars($mascota['direccion']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Historial médico -->
            <?php if (!empty($historial)): ?>
            <div class="historial-medico">
                <h3>Historial Médico Reciente</h3>
                <div class="historial-grid">
                    <?php foreach ($historial as $registro): ?>
                    <div class="historial-item">
                        <div class="historial-fecha"><?= date('d/m/Y', strtotime($registro['fecha'])) ?></div>
                        <div class="historial-descripcion"><?= htmlspecialchars($registro['descripcion']) ?></div>
                        <?php if ($registro['veterinario']): ?>
                        <div class="historial-veterinario">Dr/a: <?= htmlspecialchars($registro['veterinario']) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Identificación QR -->
            <div class="qr-section" style="margin-top:20px; text-align:center;">
                <h3>Identificación QR</h3>
                <div class="qr-wrapper" style="margin:12px auto;">
                    <img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?= urlencode($pet_url) ?>"
                        alt="QR de <?= htmlspecialchars($mascota['nombre']) ?>"
                        width="220"
                        height="220"
                        style="border-radius:12px; border:2px solid #c9a7f5; background:#fff;"
                    />
                </div>
                <div class="qr-link" style="max-width:90%; margin:0 auto;">
                    <input type="text" readonly value="<?= htmlspecialchars($pet_url) ?>" style="width:100%; padding:8px 10px; border:1px solid #ddd; border-radius:10px; font-size:14px; color:#555;" onclick="this.select()" />
                    <div style="margin-top:8px;">
                        <a href="https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=<?= urlencode($pet_url) ?>" download="qr_<?= htmlspecialchars($mascota['id_mascota']) ?>.png" style="color:#6b4fd2; text-decoration:none; font-weight:600;">Descargar QR</a>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="acciones">
                <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $mascota['id']): ?>
                    <!-- Acciones del dueño -->
                    <button type="button" class="btn-accion btn-primary" onclick="window.location.href='editar_mascota.php?id=<?= $id_mascota ?>'">
                        ✏️ Editar
                    </button>
                    <button type="button" class="btn-accion btn-danger" onclick="if(confirm('¿Estás seguro de eliminar esta mascota?')) window.location.href='eliminar_mascota.php?id=<?= $id_mascota ?>'">
                        🗑️ Eliminar
                    </button>
                <?php else: ?>
                    <!-- Acciones de terceros -->
                    <button type="button" class="btn-accion btn-contactar" onclick="contactarDueño()">
                        📞 Contactar Dueño
                    </button>
                <?php endif; ?>
                <button type="button" class="btn-accion btn-volver" onclick="window.location.href='<?= BASE_URL ?>/usuario/perfil_usuario.php'">
                    ← Volver
                </button>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../includes/bottom_nav.php'; ?>

    <script>
        function contactarDueño() {
            const telefono = '<?= htmlspecialchars($mascota['telefono'] ?? '') ?>';
            if (telefono) {
                window.location.href = 'tel:' + telefono;
            } else {
                alert('No hay información de contacto disponible.');
            }
        }
        
        function toggleEstadoForm() {
            const form = document.getElementById('estado-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            // Actualizar visibilidad de ubicación según estado inicial
            if (form.style.display === 'block') {
                actualizarVisibilidadUbicacion();
            }
        }
        
        function actualizarVisibilidadUbicacion() {
            const sel = document.getElementById('nuevo-estado');
            const box = document.getElementById('ubicacion-perdida');
            if (!sel || !box) return;
            box.style.display = sel.value === 'perdida' ? 'block' : 'none';
        }

        function guardarEstado() {
            const estado = document.getElementById('nuevo-estado').value;
            const descripcion = document.getElementById('descripcion-estado').value;
            const idMascota = <?= $id_mascota ?>;
            // Solo si es perdida tomamos ubicación
            const direccion = estado === 'perdida' ? (document.getElementById('direccion-perdida').value || '').trim() : '';
            const referencia = estado === 'perdida' ? (document.getElementById('referencia-perdida').value || '').trim() : '';
            const latitud = estado === 'perdida' ? (document.getElementById('latitud-perdida').value || '').trim() : '';
            const longitud = estado === 'perdida' ? (document.getElementById('longitud-perdida').value || '').trim() : '';
            let fechaPerdida = estado === 'perdida' ? (document.getElementById('fecha-perdida').value || '').trim() : '';
            if (fechaPerdida) {
                // datetime-local => "YYYY-MM-DDTHH:MM" -> "YYYY-MM-DD HH:MM:SS"
                fechaPerdida = fechaPerdida.replace('T', ' ');
                if (fechaPerdida.length === 16) fechaPerdida += ':00';
            }
            
            fetch('cambiar_estado.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_mascota=${idMascota}&estado=${encodeURIComponent(estado)}&descripcion=${encodeURIComponent(descripcion)}&direccion=${encodeURIComponent(direccion)}&referencia=${encodeURIComponent(referencia)}&latitud=${encodeURIComponent(latitud)}&longitud=${encodeURIComponent(longitud)}&fecha_perdida=${encodeURIComponent(fechaPerdida)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error al actualizar el estado');
                console.error(error);
            });
        }

        // Eventos al cargar
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('nuevo-estado');
            if (sel) {
                sel.addEventListener('change', actualizarVisibilidadUbicacion);
            }
            const btnGeo = document.getElementById('btn-geo');
            if (btnGeo) {
                btnGeo.addEventListener('click', () => {
                    if (!('geolocation' in navigator)) {
                        alert('La geolocalización no está disponible en este dispositivo/navegador.');
                        return;
                    }
                    const status = document.getElementById('geo-status');
                    if (status) {
                        status.textContent = 'Obteniendo ubicación actual…';
                        status.style.color = '#444';
                    }
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const { latitude, longitude } = pos.coords;
                            const latInput = document.getElementById('latitud-perdida');
                            const lngInput = document.getElementById('longitud-perdida');
                            if (latInput) latInput.value = latitude.toFixed(6);
                            if (lngInput) lngInput.value = longitude.toFixed(6);

                            // Intentar autocompletar dirección vía Nominatim (OSM)
                            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`;
                            fetch(url, { headers: { 'Accept': 'application/json' } })
                              .then(r => r.ok ? r.json() : Promise.reject(new Error('Error reverse geocoding')))
                              .then(data => {
                                  const dirInput = document.getElementById('direccion-perdida');
                                  const display = (data && data.display_name) ? String(data.display_name) : '';
                                  if (dirInput && display && !dirInput.value) {
                                      // Truncar a 255 si es muy larga
                                      dirInput.value = display.length > 255 ? display.slice(0, 255) : display;
                                  }
                                  if (status) {
                                      status.textContent = 'Ubicación obtenida. Puedes ajustar dirección o referencia si querés.';
                                      status.style.color = '#2e7d32';
                                  }
                              })
                              .catch(() => {
                                  if (status) {
                                      status.textContent = 'Coordenadas listas. No se pudo sugerir dirección automática.';
                                      status.style.color = '#b26a00';
                                  }
                              });
                        },
                        (err) => {
                            console.error(err);
                            alert('No se pudo obtener la ubicación. Verificá permisos de geolocalización.');
                            const status = document.getElementById('geo-status');
                            if (status) {
                                status.textContent = 'Error al obtener la ubicación del dispositivo.';
                                status.style.color = '#c62828';
                            }
                        },
                        { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                    );
                });
            }
        });
    </script>
</body>
</html>
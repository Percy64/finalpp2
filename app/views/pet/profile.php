<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= htmlspecialchars($pet['nombre']) ?></title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/pet-profile.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css" />
</head>
<body>
    <section class="registro-mascota">
        <div class="perfil-mascota">
            <h1 class="titulo-perfil">Perfil Mascota</h1>
            
            <!-- Foto de la mascota -->
            <div class="foto-perfil">
                <?php 
                    $fotoUrl = !empty($pet['foto_url']) ? $pet['foto_url'] : ASSETS_URL . '/images/dog-placeholder.svg';
                    if (!preg_match('/^https?:\/\//', $fotoUrl)) {
                        $fotoUrl = BASE_URL . '/' . ltrim($fotoUrl, '/');
                    }
                ?>
                <img src="<?= htmlspecialchars($fotoUrl) ?>" 
                     alt="Foto de <?= htmlspecialchars($pet['nombre']) ?>" 
                     class="imagen-mascota"
                     onerror="this.src='<?= ASSETS_URL ?>/images/dog-placeholder.svg'" />
            </div>

            <!-- Código QR - Sección mejorada -->
            <!-- Nombre de la mascota -->
            <h2 class="nombre-mascota"><?= htmlspecialchars($pet['nombre']) ?></h2>
            
            <?php if ($isOwner): ?>
            <!-- Estado de la mascota (solo para el dueño) -->
            <div class="estado-mascota">
                <?php 
                $estado_actual = $pet['estado'] ?? 'normal';
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
                <div class="<?= $clase_estado ?>">
                    <?= $texto_estado ?>
                </div>
                
                <!-- Formulario cambio de estado (solo para el dueño) -->
                <?php if ($isOwner): ?>
                <form id="form-cambiar-estado" action="<?= BASE_URL ?>/mascota/<?= $pet['id_mascota'] ?>/cambiar-estado" method="post" style="margin-top:15px;">
                    <select name="estado" onchange="onEstadoChange(this.value)" class="select-estado">
                        <option value="normal" <?= $estado_actual === 'normal' ? 'selected' : '' ?>>Normal</option>
                        <option value="perdida" <?= $estado_actual === 'perdida' ? 'selected' : '' ?>>Perdida</option>
                        <option value="encontrada" <?= $estado_actual === 'encontrada' ? 'selected' : '' ?>>Encontrada</option>
                    </select>

                    <div id="fieldset-perdida" class="fieldset-perdida" data-collapsed="<?= $estado_actual === 'perdida' ? 'true' : 'false' ?>" style="display: <?= $estado_actual === 'perdida' ? 'block' : 'none' ?>;">
                        <div class="fieldset-header">
                            <span>Datos de pérdida</span>
                            <button type="button" class="btn-toggle" onclick="togglePerdida()">Mostrar / Editar</button>
                        </div>
                        <div id="perdida-body" class="fieldset-body">
                            <label for="ubicacion_perdida">¿Dónde se perdió?</label>
                            <textarea name="ubicacion_perdida" id="ubicacion_perdida" rows="3" placeholder="Ej: Calle 123 y Av. Principal, cerca de la plaza" required><?= $estado_actual === 'perdida' ? htmlspecialchars($pet['descripcion_estado'] ?? '') : '' ?></textarea>
                            <small>Agrega referencias claras para que puedan ayudarte a encontrarla.</small>

                            <label for="descripcion_perdida" style="margin-top:10px;">Detalles adicionales (opcional)</label>
                            <textarea name="descripcion" id="descripcion_perdida" rows="3" placeholder="Color del collar, comportamiento, señales distintivas"></textarea>

                            <div class="coords-row">
                                <div class="coord-field">
                                    <label for="lat">Latitud (opcional)</label>
                                    <input type="text" name="lat" id="lat" inputmode="decimal" placeholder="-32.9408" />
                                </div>
                                <div class="coord-field">
                                    <label for="lng">Longitud (opcional)</label>
                                    <input type="text" name="lng" id="lng" inputmode="decimal" placeholder="-60.6411" />
                                </div>
                            </div>

                            <button type="button" class="btn-geo" onclick="obtenerUbicacion()">Usar ubicación automática</button>
                        </div>
                    </div>

                    <button type="button" class="btn-guardar-estado" onclick="cambiarEstado()">Guardar estado</button>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', initPerdidaVisibility);

                    function initPerdidaVisibility() {
                        const select = document.querySelector('select[name="estado"]');
                        const fieldset = document.getElementById('fieldset-perdida');
                        const body = document.getElementById('perdida-body');
                        if (!select || !fieldset || !body) return;

                        if (select.value === 'perdida') {
                            const collapsed = fieldset.dataset.collapsed === 'true';
                            fieldset.style.display = 'block';
                            body.style.display = collapsed ? 'none' : 'block';
                        } else {
                            fieldset.style.display = 'none';
                            body.style.display = 'none';
                        }
                    }

                    function togglePerdida() {
                        const fieldset = document.getElementById('fieldset-perdida');
                        const body = document.getElementById('perdida-body');
                        if (!fieldset || !body) return;
                        const isCollapsed = fieldset.dataset.collapsed === 'true';
                        fieldset.dataset.collapsed = isCollapsed ? 'false' : 'true';
                        body.style.display = isCollapsed ? 'block' : 'none';
                    }

                    function onEstadoChange(valor) {
                        const fieldset = document.getElementById('fieldset-perdida');
                        const body = document.getElementById('perdida-body');
                        const ubicacionInput = document.getElementById('ubicacion_perdida');

                        if (valor === 'perdida') {
                            fieldset.style.display = 'block';
                            body.style.display = 'block';
                            fieldset.dataset.collapsed = 'false';
                            ubicacionInput.required = true;
                        } else {
                            fieldset.style.display = 'none';
                            body.style.display = 'none';
                            fieldset.dataset.collapsed = 'true';
                            ubicacionInput.required = false;
                            ubicacionInput.value = '';
                        }
                    }

                    function cambiarEstado() {
                        const form = document.getElementById('form-cambiar-estado');
                        const formData = new FormData(form);

                        // Validar ubicación cuando se marca como perdida
                        if (formData.get('estado') === 'perdida') {
                            const ubicacion = (formData.get('ubicacion_perdida') || '').trim();
                            const lat = (formData.get('lat') || '').trim();
                            const lng = (formData.get('lng') || '').trim();
                            if (!ubicacion) {
                                alert('Por favor indica dónde se perdió');
                                return;
                            }

                            // Validar lat/lng si se ingresan
                            if (lat && isNaN(parseFloat(lat))) {
                                alert('Latitud no es válida');
                                return;
                            }
                            if (lng && isNaN(parseFloat(lng))) {
                                alert('Longitud no es válida');
                                return;
                            }
                        }
                        
                        fetch(form.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Minimizar la sección de pérdida tras guardar
                                const fieldset = document.getElementById('fieldset-perdida');
                                const body = document.getElementById('perdida-body');
                                if (formData.get('estado') === 'perdida') {
                                    fieldset.dataset.collapsed = 'true';
                                    body.style.display = 'none';
                                }
                                location.reload();
                            } else {
                                alert('Error: ' + (data.message || 'No se pudo cambiar el estado'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al cambiar el estado');
                        });
                    }

                    function obtenerUbicacion() {
                        if (!navigator.geolocation) {
                            alert('Tu navegador no soporta geolocalización.');
                            return;
                        }

                        const btn = document.querySelector('.btn-geo');
                        btn.disabled = true;
                        btn.textContent = 'Obteniendo ubicación...';

                        navigator.geolocation.getCurrentPosition(
                            (pos) => {
                                const lat = pos.coords.latitude.toFixed(6);
                                const lng = pos.coords.longitude.toFixed(6);
                                document.getElementById('lat').value = lat;
                                document.getElementById('lng').value = lng;

                                // Si la ubicación está vacía, sugerimos las coords
                                const ubicacionInput = document.getElementById('ubicacion_perdida');
                                if (!ubicacionInput.value.trim()) {
                                    ubicacionInput.value = `Coordenadas aproximadas: ${lat}, ${lng}`;
                                }

                                btn.disabled = false;
                                btn.textContent = 'Usar ubicación automática';
                            },
                            (err) => {
                                console.error('Geo error', err);
                                alert('No se pudo obtener ubicación. Revisa permisos o intenta de nuevo.');
                                btn.disabled = false;
                                btn.textContent = 'Usar ubicación automática';
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    }
                </script>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Información básica -->
            <div class="info-basica">
                <div class="info-item">
                    <span class="label">Especie:</span>
                    <span class="valor"><?= htmlspecialchars(ucfirst($pet['especie'])) ?></span>
                </div>
                <?php if (!empty($pet['raza'])): ?>
                <div class="info-item">
                    <span class="label">Raza:</span>
                    <span class="valor"><?= htmlspecialchars($pet['raza']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($pet['edad'])): ?>
                <div class="info-item">
                    <span class="label">Edad:</span>
                    <span class="valor"><?= htmlspecialchars($pet['edad']) ?> años</span>
                </div>
                <?php endif; ?>
                <?php if (!empty($pet['color'])): ?>
                <div class="info-item">
                    <span class="label">Color:</span>
                    <span class="valor"><?= htmlspecialchars($pet['color']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($pet['genero'])): ?>
                <div class="info-item">
                    <span class="label">Género:</span>
                    <span class="valor"><?= htmlspecialchars(ucfirst($pet['genero'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($pet['descripcion'])): ?>
                <div class="info-item">
                    <span class="label">Descripción:</span>
                    <span class="valor"><?= htmlspecialchars($pet['descripcion']) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Información del dueño -->
            <div class="info-dueno">
                <h3>Información del Dueño</h3>
                <div class="info-item">
                    <span class="label">Nombre:</span>
                    <span class="valor"><?= htmlspecialchars($pet['nombre_dueno'] ?? 'No disponible') ?></span>
                </div>
                <?php if (!empty($pet['telefono_dueno'])): ?>
                <div class="info-item">
                    <span class="label">Teléfono:</span>
                    <span class="valor">
                        <a href="tel:<?= htmlspecialchars($pet['telefono_dueno']) ?>"><?= htmlspecialchars($pet['telefono_dueno']) ?></a>
                    </span>
                </div>
                <?php endif; ?>
                <?php if (!empty($pet['email_dueno'])): ?>
                <div class="info-item">
                    <span class="label">Email:</span>
                    <span class="valor">
                        <a href="mailto:<?= htmlspecialchars($pet['email_dueno']) ?>"><?= htmlspecialchars($pet['email_dueno']) ?></a>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Botones de acción (solo para el dueño) -->
            <?php if ($isOwner): ?>
            <div class="acciones-perfil">
                <button class="btn-editar" onclick="window.location.href='<?= BASE_URL ?>/mascota/<?= $pet['id_mascota'] ?>/editar'">✏️ Editar</button>
                <button class="btn-eliminar" onclick="if(confirm('¿Estás seguro de que deseas eliminar a <?= addslashes(htmlspecialchars($pet['nombre'])) ?>?')) { window.location.href='<?= BASE_URL ?>/mascota/<?= $pet['id_mascota'] ?>/eliminar'; }">🗑️ Eliminar</button>
            </div>
            <?php endif; ?>

            <!-- Código QR (al final) -->
            <div class="qr-section">
                <h3>Código QR de Identificación</h3>
                <?php 
                    // Obtener IPv4 del servidor de forma confiable
                    $ipv4 = '192.168.0.10'; // IP fija de la máquina
                    // Alternativa: obtener de $_SERVER si es disponible
                    if (!empty($_SERVER['SERVER_ADDR']) && strpos($_SERVER['SERVER_ADDR'], ':') === false) {
                        $ipv4 = $_SERVER['SERVER_ADDR'];
                    }
                    
                    // URL que apunta a qr_info usando la ruta amigable
                    $qr_target_url = "http://" . $ipv4 . "/finalpp2/qr/" . $pet['id_mascota'];
                    
                    // Generar QR con la URL IPv4
                    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qr_target_url);
                ?>
                
                <?php if ($qr_url): ?>
                <div class="qr-display">
                    <a href="<?= htmlspecialchars($qr_target_url) ?>" target="_blank" title="Abre el perfil de <?= htmlspecialchars($pet['nombre']) ?>">
                        <img src="<?= htmlspecialchars($qr_url) ?>" 
                             alt="Código QR para <?= htmlspecialchars($pet['nombre']) ?>" 
                             class="qr-image"
                             title="Escanea este QR para ver el perfil de <?= htmlspecialchars($pet['nombre']) ?>">
                    </a>
                </div>
                <p class="qr-info">Escanea este código QR con tu teléfono para compartir el perfil de tu mascota</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Barra de navegación inferior -->
    <?php include ROOT_PATH . '/app/includes/bottom_nav.php'; ?>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Mascotas - Mapa</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/pet-map.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
</head>
<body>
    <div class="mapa-container">
        <h1>🔍 Buscar Mascotas Perdidas</h1>

        <!-- Estadísticas resumidas -->
        <div style="margin: 20px 0; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <div style="background: white; border-radius: 12px; padding: 18px 16px; text-align: center; flex: 1; min-width: 120px; max-width: 160px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);">
                <strong style="font-size: 22px; display: block; color: #9B44CE; margin-bottom: 6px;"><?= (int)($estadisticas['reunidas_mes'] ?? 0) ?></strong>
                <span style="font-size: 12px; color: #666; font-weight: 500;">Mascotas reunidas este mes</span>
            </div>
            <div style="background: white; border-radius: 12px; padding: 18px 16px; text-align: center; flex: 1; min-width: 120px; max-width: 160px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);">
                <strong style="font-size: 22px; display: block; color: #9B44CE; margin-bottom: 6px;"><?= (int)($estadisticas['usuarios_activos'] ?? 0) ?></strong>
                <span style="font-size: 12px; color: #666; font-weight: 500;">Usuarios activos</span>
            </div>
            <div style="background: white; border-radius: 12px; padding: 18px 16px; text-align: center; flex: 1; min-width: 120px; max-width: 160px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);">
                <strong style="font-size: 22px; display: block; color: #9B44CE; margin-bottom: 6px;">24/7</strong>
                <span style="font-size: 12px; color: #666; font-weight: 500;">Soporte disponible</span>
            </div>
        </div>

        <!-- Google Maps -->
        <div id="map"></div>

        <!-- Lista de mascotas -->
        <div class="lista-mascotas">
            <h2 style="margin-top: 0;">📋 Mascotas Disponibles</h2>
            <div class="results-container" id="results">
                    <?php if (empty($mascotas)): ?>
                        <p style="text-align: center; color: #666; padding: 40px;">No hay mascotas perdidas registradas en este momento.</p>
                    <?php else: ?>
                        <?php foreach ($mascotas as $mascota): ?>
                            <?php
                                $fotoUrl = !empty($mascota['foto_url']) ? $mascota['foto_url'] : ASSETS_URL . '/images/dog-placeholder.svg';
                                if (!preg_match('/^https?:\/\//', $fotoUrl)) {
                                    $fotoUrl = BASE_URL . '/' . ltrim($fotoUrl, '/');
                                }
                            ?>
                            <div class="mascota-item" data-mascota-id="<?= $mascota['id_mascota'] ?>" onclick="window.location.href='<?= BASE_URL ?>/mascota/<?= $mascota['id_mascota'] ?>'">
                                <div class="mascota-foto" style="background: #ddd; display: flex; align-items: center; justify-content: center; color: #999; position: relative; overflow: hidden;">
                                    <?php if (!empty($mascota['foto_url'])): ?>
                                        <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto de <?= htmlspecialchars($mascota['nombre']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none';">
                                    <?php else: ?>
                                        <span>Sin foto</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mascota-info">
                                    <div class="mascota-nombre"><?= htmlspecialchars($mascota['nombre']) ?></div>
                                    <div class="mascota-detalles">
                                        🐾 Especie: <strong><?= htmlspecialchars($mascota['especie']) ?></strong>
                                        <?php if (!empty($mascota['raza'])): ?>
                                            | Raza: <strong><?= htmlspecialchars($mascota['raza']) ?></strong>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($mascota['color'])): ?>
                                        <div class="mascota-detalles">🎨 Color: <strong><?= htmlspecialchars($mascota['color']) ?></strong></div>
                                    <?php endif; ?>
                                    <?php if (!empty($mascota['descripcion'])): ?>
                                        <div class="mascota-detalles">📝 <?= htmlspecialchars($mascota['descripcion']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($mascota['ubicacion'])): ?>
                                        <div class="mascota-detalles">📍 <?= htmlspecialchars($mascota['ubicacion']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($mascota['fecha_perdida'])): ?>
                                        <div class="mascota-detalles">📅 Reportado: <?= date('d/m/Y', strtotime($mascota['fecha_perdida'] ?? $mascota['fecha_estado'] ?? $mascota['fecha_registro'])) ?></div>
                                    <?php endif; ?>
                                    <span class="estado-badge <?= ($mascota['estado'] ?? 'normal') === 'perdida' ? 'estado-perdida' : 'estado-normal' ?>">
                                        <?= ($mascota['estado'] ?? 'normal') === 'perdida' ? '🔴 PERDIDA' : '✅ Normal' ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de navegación inferior -->
    <?php include ROOT_PATH . '/app/includes/bottom_nav.php'; ?>

    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    
    <script>
        // Inicializar mapa con Leaflet
        const map = L.map('map').setView([-32.9387, -60.6611], 13);
        
        // Agregar capa de mapa (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Datos de mascotas desde PHP
        const mascotasData = <?php echo json_encode($mascotas ?? []); ?>;
        
        // Crear marcadores
        const markers = [];

        mascotasData.forEach((mascota, index) => {
            // Usar coordenadas de mascotas_perdidas si existen
            const lat = mascota.latitud ? parseFloat(mascota.latitud) : -32.9387 + (Math.random() - 0.5) * 0.1;
            const lng = mascota.longitud ? parseFloat(mascota.longitud) : -60.6611 + (Math.random() - 0.5) * 0.1;

            // Color del marcador según estado
            const color = mascota.estado === 'perdida' ? 'red' : 'blue';

            const marker = L.circleMarker([lat, lng], {
                radius: 12,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            // Popup con información
            const popupContent = `
                <div style="width: 200px;">
                    <h4 style="margin: 0 0 8px 0;">${mascota.nombre}</h4>
                    <p style="margin: 4px 0;"><strong>Especie:</strong> ${mascota.especie}</p>
                    ${mascota.raza ? `<p style="margin: 4px 0;"><strong>Raza:</strong> ${mascota.raza}</p>` : ''}
                    ${mascota.color ? `<p style="margin: 4px 0;"><strong>Color:</strong> ${mascota.color}</p>` : ''}
                    ${mascota.ubicacion ? `<p style="margin: 4px 0;"><strong>📍 Ubicación:</strong> ${mascota.ubicacion}</p>` : ''}
                    <p style="margin: 4px 0;"><strong>Estado:</strong> ${mascota.estado === 'perdida' ? '🔴 PERDIDA' : '✅ Normal'}</p>
                    <a href="<?= BASE_URL ?>/mascota/${mascota.id_mascota}" style="color: #6b4fd2; text-decoration: none; font-weight: bold; display: block; margin-top: 8px;">Ver detalles →</a>
                </div>
            `;

            marker.bindPopup(popupContent);

            // Al hacer click en marcador
            marker.on('click', () => {
                // Highlight en lista
                document.querySelectorAll('.mascota-item').forEach(item => item.style.opacity = '0.5');
                const itemElement = document.querySelector(`[data-mascota-id="${mascota.id_mascota}"]`);
                if (itemElement) {
                    itemElement.style.opacity = '1';
                    itemElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            markers.push({ marker: marker, mascota: mascota, index: index });
        });

        // Interacción con elementos de lista
        document.querySelectorAll('.mascota-item').forEach((item, index) => {
            item.addEventListener('mouseenter', () => {
                if (markers[index]) {
                    // Centrar mapa en marcador
                    map.setView(markers[index].marker.getLatLng(), 15);
                    markers[index].marker.openPopup();
                }
            });
            
            item.addEventListener('mouseleave', () => {
                if (markers[index]) {
                    markers[index].marker.closePopup();
                }
            });
        });

        // Ajustar zoom si hay muchas mascotas
        if (mascotasData.length > 1) {
            const group = new L.featureGroup(markers.map(m => m.marker));
            map.fitBounds(group.getBounds().pad(0.1));
        }
    </script>
</body>
</html>

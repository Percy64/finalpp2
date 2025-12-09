<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php';

$mascotas_perdidas = [];

try {
  $sql = "SELECT m.*, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario,
                 mp.direccion AS direccion_perdida,
                 mp.latitud   AS lat_perdida,
                 mp.longitud  AS lng_perdida,
                 mp.fecha_perdida
      FROM mascotas m
      LEFT JOIN usuarios u ON m.id = u.id
      LEFT JOIN (
        SELECT p1.id_mascota, p1.direccion, p1.latitud, p1.longitud, p1.fecha_perdida, p1.creado_en
        FROM mascotas_perdidas p1
        INNER JOIN (
          SELECT id_mascota, MAX(COALESCE(fecha_perdida, creado_en)) AS last_date
          FROM mascotas_perdidas
          GROUP BY id_mascota
        ) p2 ON p1.id_mascota = p2.id_mascota AND COALESCE(p1.fecha_perdida, p1.creado_en) = p2.last_date
      ) mp ON mp.id_mascota = m.id_mascota
      WHERE (m.estado = 'perdida' OR m.perdido = 1)
      ORDER BY COALESCE(m.fecha_estado, m.fecha_registro) DESC
      LIMIT 20";
  $stmt = $pdo->query($sql);
  $mascotas_perdidas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log('Error al cargar mascotas perdidas: ' . $e->getMessage());
}

function obtenerFotoMascota(array $mascota): string
{
  if (!empty($mascota['foto_url'])) {
    $ruta_relativa = ltrim($mascota['foto_url'], '/');
    $ruta_local = ROOT_PATH . '/' . $ruta_relativa;
    if (file_exists($ruta_local)) {
      return BASE_URL . '/' . $ruta_relativa;
    }
  }
  return ASSETS_URL . '/images/dog-placeholder.svg';
}

function obtenerNombreCompleto(array $mascota): string
{
  $nombre = $mascota['nombre_usuario'] ?? '';
  $apellido = $mascota['apellido_usuario'] ?? '';
  return trim($nombre . ' ' . $apellido);
}

function formatoUbicacion(array $m): string
{
  // Prioriza dirección; si no hay, usa lat/lng con 5 decimales
  if (!empty($m['direccion_perdida'])) {
    return $m['direccion_perdida'];
  }
  $lat = isset($m['lat_perdida']) ? trim((string)$m['lat_perdida']) : '';
  $lng = isset($m['lng_perdida']) ? trim((string)$m['lng_perdida']) : '';
  if ($lat !== '' && $lng !== '' && is_numeric($lat) && is_numeric($lng)) {
    return round((float)$lat, 5) . ', ' . round((float)$lng, 5);
  }
  return '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mascotas Perdidas</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/home3_03.css" />
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css" />
  <style>
    /* Asegurar imágenes uniformes en el carrusel de mascotas perdidas */
    .carrusel .card-mascota img {
      width: 180px;
      height: 180px;
      object-fit: cover; /* recorta manteniendo el foco */
      border-radius: 12px;
      background-color: #f2f2f2; /* placeholder de fondo si tarda en cargar */
      display: block;
      margin: 0 auto; /* centrar dentro de la tarjeta */
    }
      /* Centrar y dimensionar el mapa */
      #mapa { max-width: 900px; height: 320px; margin: 0 auto 16px; }
      /* Layout base optimizado */
      .carrusel-con-flechas { position: relative; }
      .carrusel { display: flex; gap: 12px; overflow-x: auto; scroll-behavior: smooth; padding: 8px 4px; }
      .carrusel::-webkit-scrollbar { height: 6px; }
      .carrusel::-webkit-scrollbar-thumb { background: #c9a7f5; border-radius: 3px; }
    .card-mascota { flex: 0 0 auto; width: 200px; background:#fff; border-radius:14px; box-shadow:0 4px 14px rgba(0,0,0,0.08); padding:10px; display:flex; flex-direction:column; align-items:center; cursor:pointer; }
      .card-mascota p { font-size:14px; line-height:1.25; margin:6px 0 0; text-align:center; }
      .card-mascota span { font-size:12px; color:#555; margin-top:6px; text-align:center; display:block; }
  .card-mascota .loc { font-size:12px; color:#444; margin:4px 0 0; text-align:center; }
    .btn-ver-mapa { margin-top:6px; font-size:12px; background:#f2f0ff; color:#4b3dbb; border:1px solid #d6cdfd; border-radius:8px; padding:6px 10px; cursor:pointer; }
    .btn-ver-mapa:hover { background:#eae6ff; }
      .flecha { background:#6b4fd2; color:#fff; border:none; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.15); }
      .flecha:active { transform:scale(.92); }
      .flecha.izquierda { position:absolute; left:-4px; top:50%; transform:translateY(-50%); }
      .flecha.derecha { position:absolute; right:-4px; top:50%; transform:translateY(-50%); }
    
      /* Mobile adaptations */
      @media (max-width: 480px) {
        .carrusel .card-mascota img { width: 140px; height:140px; }
        .card-mascota { width:160px; padding:8px; }
        .card-mascota p { font-size:13px; }
        .card-mascota span { font-size:11px; }
        .flecha { width:32px; height:32px; font-size:20px; }
        #mapa { height:240px; margin: 0 auto 12px; }
        .busqueda-mascota h2 { font-size:20px; margin-bottom:10px; }
      }
    
      /* Scroll snap for smooth card stop (mobile) */
      @media (max-width: 600px) {
        .carrusel { scroll-snap-type: x mandatory; }
        .card-mascota { scroll-snap-align: start; }
      }
    
      /* Preferencia de usuario: reduce motion */
      @media (prefers-reduced-motion: reduce) {
        .carrusel { scroll-behavior: auto; }
        .flecha { transition:none; }
      }
    
      /* Accesibilidad foco */
      .card-mascota:focus { outline:3px solid #6b4fd2; }
  </style>
</head>
<body>

  <section class="contenedor-principal">
    <!-- Bloque de búsqueda -->
    <div class="busqueda-mascota">
      <h2>Mascotas Reportadas como Perdidas</h2>

      <div id="mapa" style="width:100%; height:320px; border-radius:12px; overflow:hidden;">
        <!-- Mapa interactivo Leaflet -->
      </div>

      <!-- Carrusel -->
      <div class="carrusel-con-flechas">
        <button class="flecha izquierda">‹</button>

        <div class="carrusel">
          <?php if (!empty($mascotas_perdidas)): ?>
            <?php foreach ($mascotas_perdidas as $mascota): ?>
              <?php
                $imagen = obtenerFotoMascota($mascota);
                $descripcion = !empty($mascota['descripcion_estado']) ? $mascota['descripcion_estado'] : 'Mascota reportada como perdida';
                $reportante = obtenerNombreCompleto($mascota);
                $fechaEvento = $mascota['fecha_estado'] ?? $mascota['fecha_registro'];
                $fechaLegible = $fechaEvento ? date('d/m H:i', strtotime($fechaEvento)) : '';
                $ubicacion = formatoUbicacion($mascota);
              ?>
              <div class="card-mascota" tabindex="0" data-url="<?= BASE_URL ?>/mascota/perfil_mascota.php?id=<?= $mascota['id_mascota'] ?>" data-lat="<?= htmlspecialchars($mascota['lat_perdida'] ?? '') ?>" data-lng="<?= htmlspecialchars($mascota['lng_perdida'] ?? '') ?>" data-dir="<?= htmlspecialchars($mascota['direccion_perdida'] ?? '') ?>" onclick="window.location.href='<?= BASE_URL ?>/mascota/perfil_mascota.php?id=<?= $mascota['id_mascota'] ?>'" onkeypress="if(event.key==='Enter'){window.location.href='<?= BASE_URL ?>/mascota/perfil_mascota.php?id=<?= $mascota['id_mascota'] ?>'}">
                <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($mascota['nombre']) ?>" loading="lazy" width="180" height="180" />
                <p><strong><?= htmlspecialchars($mascota['nombre']) ?></strong></p>
                <p><?= htmlspecialchars($descripcion) ?></p>
                <?php if ($ubicacion !== ''): ?>
                  <p class="loc">📍 <?= htmlspecialchars($ubicacion) ?></p>
                  <button type="button" class="btn-ver-mapa" aria-label="Ver ubicación en el mapa">Ver en mapa</button>
                <?php endif; ?>
                <span>
                  <?php if ($reportante): ?>
                    👤 <?= htmlspecialchars($reportante) ?>
                  <?php endif; ?>
                  <?php if ($fechaLegible): ?>
                    &nbsp;&nbsp;🕒 <?= $fechaLegible ?>
                  <?php endif; ?>
                </span>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="card-mascota" style="justify-content:center; text-align:center;">
              <p>No hay mascotas reportadas como perdidas en este momento.</p>
            </div>
          <?php endif; ?>
        </div>

        <button class="flecha derecha">›</button>
      </div>
      <!-- Fin carrusel -->
    </div>

    
  </section>

  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
  />
  <script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
  <script>
    const carrusel = document.querySelector('.carrusel');
    const btnIzq = document.querySelector('.flecha.izquierda');
    const btnDer = document.querySelector('.flecha.derecha');
    const mapaDiv = document.getElementById('mapa');
    let map; // instancia Leaflet
    const markersById = {}; // para reusar / centrar

    if (carrusel && btnIzq && btnDer) {
      btnIzq.addEventListener('click', () => {
        carrusel.scrollBy({ left: -250, behavior: 'smooth' });
      });

      btnDer.addEventListener('click', () => {
        carrusel.scrollBy({ left: 250, behavior: 'smooth' });
      });
    }

    // Geocodificación de direcciones (para tarjetas sin lat/lng)
    const geocodeCache = new Map();
    function geocodeDireccion(direccion) {
      if (!direccion) return Promise.resolve(null);
      if (geocodeCache.has(direccion)) return Promise.resolve(geocodeCache.get(direccion));
      const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(direccion)}`;
      return fetch(url, { headers: { 'Accept': 'application/json' }})
        .then(r => r.ok ? r.json() : Promise.reject(new Error('Geocode error')))
        .then(data => {
          if (Array.isArray(data) && data.length) {
            const res = { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
            geocodeCache.set(direccion, res);
            return res;
          }
          return null;
        })
        .catch(() => null);
    }

    async function initLeaflet() {
      // Determinar centro inicial (primera mascota con coords) o fallback Rosario
      let center = [-32.94424, -60.63932];
      const firstWithCoords = document.querySelector('.card-mascota[data-lat][data-lng]');
      if (firstWithCoords) {
        const lt = parseFloat(firstWithCoords.dataset.lat);
        const lg = parseFloat(firstWithCoords.dataset.lng);
        if (!isNaN(lt) && !isNaN(lg)) center = [lt, lg];
      }
      map = L.map('mapa', { scrollWheelZoom: true }).setView(center, 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contrib.'
      }).addTo(map);

      // Agregar marcadores
      const bounds = L.latLngBounds();
      const cards = Array.from(document.querySelectorAll('.card-mascota'));
      const pendingGeocode = [];
      cards.forEach(card => {
        const lat = parseFloat(card.dataset.lat);
        const lng = parseFloat(card.dataset.lng);
        if (!isNaN(lat) && !isNaN(lng)) {
            const nombre = card.querySelector('strong')?.textContent || 'Mascota';
            const ubicacion = card.querySelector('.loc')?.textContent?.replace('📍','').trim() || '';
            const perfilUrl = card.dataset.url || '#';
            const idMascota = perfilUrl.split('id=').pop();
            const marker = L.marker([lat, lng]).addTo(map).bindPopup(`<div style="min-width:160px"><strong>${nombre}</strong><br>${ubicacion ? '📍 ' + ubicacion : ''}<br><a href="${perfilUrl}">Ver perfil</a></div>`);
          if (idMascota) markersById[idMascota] = marker;
          // Hover highlight (opcional)
          card.addEventListener('mouseenter', () => { marker.openPopup(); });
          bounds.extend([lat, lng]);
        } else {
          const dir = (card.dataset.dir || '').trim();
          if (dir) pendingGeocode.push({ card, dir });
        }
      });

      // Geocodificar direcciones sin coords (secuencial con pequeño delay)
      for (const item of pendingGeocode) {
        const res = await geocodeDireccion(item.dir);
        if (res && !isNaN(res.lat) && !isNaN(res.lng)) {
          const { card } = item;
          const nombre = card.querySelector('strong')?.textContent || 'Mascota';
          const perfilUrl = card.dataset.url || '#';
          const ubicacion = (card.querySelector('.loc')?.textContent || '').replace('📍','').trim();
          const marker = L.marker([res.lat, res.lng]).addTo(map).bindPopup(`<div style="min-width:160px"><strong>${nombre}</strong><br>${ubicacion ? '📍 ' + ubicacion : ''}<br><a href="${perfilUrl}">Ver perfil</a></div>`);
          bounds.extend([res.lat, res.lng]);
        }
        // Pequeña espera para ser gentiles con el servicio
        await new Promise(r => setTimeout(r, 250));
      }

      // Ajustar vista para incluir todos los marcadores
      if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [20, 20], maxZoom: 16 });
      }
    }

    // Botones "Ver en mapa"
    document.querySelectorAll('.btn-ver-mapa').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.stopPropagation();
        const card = btn.closest('.card-mascota');
        const lat = parseFloat(card?.dataset.lat || '');
        const lng = parseFloat(card?.dataset.lng || '');
        const direccion = (card?.dataset.dir || '').trim();
        if (map && !isNaN(lat) && !isNaN(lng)) {
          map.setView([lat, lng], 15, { animate: true });
          return;
        }
        if (map && direccion) {
          const res = await geocodeDireccion(direccion);
          if (res && !isNaN(res.lat) && !isNaN(res.lng)) {
            const nombre = card.querySelector('strong')?.textContent || 'Mascota';
            const perfilUrl = card.dataset.url || '#';
            map.setView([res.lat, res.lng], 15, { animate: true });
            L.marker([res.lat, res.lng]).addTo(map).bindPopup(`<div style="min-width:160px"><strong>${nombre}</strong><br>📍 ${direccion}<br><a href="${perfilUrl}">Ver perfil</a></div>`).openPopup();
          }
        }
      });
    });

    // Inicializar mapa interactivo
    initLeaflet();
  </script>

  <?php include __DIR__ . '/../includes/bottom_nav.php'; ?>
</body>
</html>

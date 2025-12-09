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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8f9fa;
            color: #2c3e50;
        }

        .content-wrapper {
            background: #fff;
        }

        /* Hero Section - Minimalista y Moderno */
        .hero {
            background: linear-gradient(180deg, #fff 0%, #f0f3ff 100%);
            padding: 100px 30px;
            text-align: center;
            position: relative;
            border-bottom: 1px solid #e8eef7;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -2px;
        }

        .hero p {
            font-size: 1.25rem;
            color: #64748b;
            font-weight: 400;
            margin-bottom: 35px;
        }

        .hero-cta {
            display: inline-flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-primary, .btn-secondary {
            padding: 14px 35px;
            border-radius: 10px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #f0f3ff;
            transform: translateY(-2px);
        }

        /* Stats Section */
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            padding: 50px 30px;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .stat-card {
            padding: 25px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.95rem;
            margin-top: 8px;
            font-weight: 500;
        }

        /* Intro Section */
        .intro-section {
            padding: 80px 30px;
            background: #fff;
        }

        .intro-content h2 {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 60px;
            color: #1a202c;
            font-weight: 800;
            position: relative;
        }

        .intro-content h2::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 20px auto 0;
        }

        /* Carrusel Mejorado */
        .carousel-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .carousel-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 25px 50px rgba(102, 126, 234, 0.25);
        }

        .carousel {
            display: flex;
            transition: transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .carousel-slide {
            min-width: 100%;
            padding: 70px 50px;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }

        .carousel-icon {
            font-size: 100px;
            margin-bottom: 25px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .carousel-slide h3 {
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .carousel-slide p {
            font-size: 1.05rem;
            opacity: 0.95;
            max-width: 500px;
            line-height: 1.8;
            font-weight: 300;
        }

        .carousel-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            padding: 30px 0;
        }

        .carousel-btn {
            background: white;
            color: #667eea;
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .carousel-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .carousel-dots {
            display: flex;
            gap: 10px;
        }

        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-dot.active {
            background: white;
            width: 32px;
            border-radius: 6px;
        }

        .carousel-dot:hover {
            background: rgba(255, 255, 255, 0.7);
        }

        /* Novedades */
        .novedades {
            background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);
            padding: 50px 40px;
            text-align: center;
            margin: 60px 30px;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }

        .novedades::before {
            content: '✨';
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 40px;
            opacity: 0.3;
        }

        .novedades h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #92400e;
            font-weight: 700;
        }

        .novedades p {
            font-size: 1rem;
            color: #b45309;
            line-height: 1.7;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Section Head */
        .section-head {
            padding: 60px 30px 30px;
            text-align: center;
        }

        .section-head h2 {
            font-size: 2.5rem;
            color: #1a202c;
            margin-bottom: 10px;
            font-weight: 800;
            position: relative;
        }

        .section-head h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 15px auto 0;
        }

        .section-head p {
            font-size: 1.05rem;
            color: #667eea;
            font-weight: 600;
            margin-top: 15px;
        }

        /* Pet Cards Grid */
        .mascotas {
            padding: 40px 30px;
        }

        .mascotas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .pet-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            cursor: pointer;
            border: 1px solid #f0f4ff;
        }

        .pet-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
        }

        .pet-image-container {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #f0f4ff, #e9ecef);
            position: relative;
            overflow: hidden;
        }

        .pet-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .pet-card:hover .pet-image {
            transform: scale(1.1);
        }

        .pet-header {
            padding: 24px;
        }

        .pet-name {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 8px;
        }

        .pet-description {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 15px;
        }

        .pet-location {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #667eea;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .pet-location svg {
            width: 16px;
            height: 16px;
        }

        .pet-detalles {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }

        .chip {
            display: inline-block;
            background: #f0f3ff;
            color: #667eea;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .estado-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-top: 15px;
        }

        .estado-perdida {
            background: #fee2e2;
            color: #991b1b;
        }

        .estado-normal {
            background: #dcfce7;
            color: #166534;
        }

        /* Actions Footer */
        .actions-footer {
            padding: 60px 30px;
            text-align: center;
        }

        .btn-reportar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 60px;
            font-size: 1.1rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .btn-reportar:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        /* Footer */
        .site-footer {
            background: transparent;
            color: #f3f4f6;
            padding: 50px 30px;
            margin-top: 80px;
            border-top: 1px solid #e8eef7;
        }

        .site-footer__inner {
            text-align: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .site-footer__brand {
            font-size: 1.8rem;
            font-weight: 900;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .site-footer__copy {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 20px;
        }

        .site-footer__links {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .site-footer__links a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 0.95rem;
        }

        .site-footer__links a:hover {
            color: #667eea;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                padding: 60px 20px;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .hero-cta {
                flex-direction: column;
                gap: 12px;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
            }

            .intro-content h2 {
                font-size: 2rem;
            }

            .carousel-slide {
                padding: 50px 30px;
            }

            .carousel-slide h3 {
                font-size: 1.5rem;
            }

            .carousel-icon {
                font-size: 80px;
            }

            .section-head h2 {
                font-size: 1.8rem;
            }

            .mascotas-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }

            .stats-section {
                gap: 20px;
                padding: 40px 20px;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .intro-content h2 {
                font-size: 1.5rem;
            }

            .carousel-slide {
                padding: 40px 20px;
            }

            .carousel-slide h3 {
                font-size: 1.3rem;
            }

            .carousel-slide p {
                font-size: 0.95rem;
            }

            .carousel-icon {
                font-size: 60px;
            }

            .mascotas-grid {
                grid-template-columns: 1fr;
            }

            .section-head h2 {
                font-size: 1.5rem;
            }

            .btn-reportar {
                padding: 12px 40px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <!-- Encabezado -->
        <header class="hero">
            <h1>lost & Found</h1>
            <p>Ayudando a reunir familias con sus mascotas perdidas</p>
            <div class="hero-cta">
                <a href="<?= BASE_URL ?>/registrar-mascota" class="btn-primary">Reportar Mascota</a>
                <a href="<?= BASE_URL ?>/mapa" class="btn-secondary">Ver Mapa</a>
            </div>
        </header>

        <!-- Estadísticas -->
        <section class="stats-section" style="display: none;">
            <div class="stat-card">
                <div class="stat-number">2,547</div>
                <div class="stat-label">Mascotas Registradas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">1,823</div>
                <div class="stat-label">Reunidas Exitosamente</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">3,421</div>
                <div class="stat-label">Usuarios Activos</div>
            </div>
        </section>

        <!-- Sección Introductoria -->
        <section class="intro-section">
            <div class="intro-content">
                <h2>¿Cómo funciona?</h2>
                
                <!-- Carrusel Interactivo -->
                <div class="carousel-container">
                    <div class="carousel-wrapper">
                        <div class="carousel" id="carousel" style="transform: translateX(0%);">
                            <div class="carousel-slide">
                                <div class="carousel-icon">🐾</div>
                                <h3>Registra tu Mascota</h3>
                                <p>Sube la foto y datos de tu mascota para crear un perfil único con código QR</p>
                            </div>
                            <div class="carousel-slide">
                                <div class="carousel-icon">📍</div>
                                <h3>Marca como Perdida</h3>
                                <p>Si tu mascota se pierde, registra la ubicación en el mapa para que otros la encuentren</p>
                            </div>
                            <div class="carousel-slide">
                                <div class="carousel-icon">🔗</div>
                                <h3>Comparte el QR</h3>
                                <p>Comparte el código QR en redes sociales para que te ayuden a buscar</p>
                            </div>
                            <div class="carousel-slide">
                                <div class="carousel-icon">❤️</div>
                                <h3>Encuentra tu Mascota</h3>
                                <p>Otros usuarios pueden reportar avistamientos a través del mapa interactivo</p>
                            </div>
                        </div>
                    </div>

                    <!-- Controles del Carrusel -->
                    <div class="carousel-controls">
                        <button class="carousel-btn" onclick="previousSlide()">❮</button>
                        <div class="carousel-dots">
                            <span class="carousel-dot active" onclick="goToSlide(0)"></span>
                            <span class="carousel-dot" onclick="goToSlide(1)"></span>
                            <span class="carousel-dot" onclick="goToSlide(2)"></span>
                            <span class="carousel-dot" onclick="goToSlide(3)"></span>
                        </div>
                        <button class="carousel-btn" onclick="nextSlide()">❯</button>
                    </div>
                </div>

                <!-- Vista de tarjetas para pantallas grandes -->
                <div class="intro-grid" style="display: none;">
                    <div class="intro-card">
                        <div class="intro-icon">🐾</div>
                        <h3>Registra tu Mascota</h3>
                        <p>Sube la foto y datos de tu mascota para crear un perfil único con código QR</p>
                    </div>
                    <div class="intro-card">
                        <div class="intro-icon">📍</div>
                        <h3>Marca como Perdida</h3>
                        <p>Si tu mascota se pierde, registra la ubicación en el mapa para que otros la encuentren</p>
                    </div>
                    <div class="intro-card">
                        <div class="intro-icon">🔗</div>
                        <h3>Comparte el QR</h3>
                        <p>Comparte el código QR en redes sociales para que te ayuden a buscar</p>
                    </div>
                    <div class="intro-card">
                        <div class="intro-icon">❤️</div>
                        <h3>Encuentra tu Mascota</h3>
                        <p>Otros usuarios pueden reportar avistamientos a través del mapa interactivo</p>
                    </div>
                </div>
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
                    <!-- Estado vacío -->
                <?php else: ?>
                    <?php foreach ($mascotas as $mascota): ?>
                        <?php
                            $descripcion = $mascota['descripcion_estado'] ?: 'Mascota perdida';
                            $ubicacion = !empty($mascota['ubicacion']) ? $mascota['ubicacion'] : 'Ubicación no especificada';
                            
                            $foto = $mascota['foto_url'] ?? '';
                            if ($foto && !preg_match('/^https?:\/\//', $foto)) {
                                $foto_normalizada = preg_replace('#^\.\./#', '', $foto);
                                $foto = BASE_URL . '/' . ltrim($foto_normalizada, '/');
                            }
                            
                            $chips = [];
                            if (!empty($mascota['especie'])) $chips[] = ucfirst($mascota['especie']);
                            if (!empty($mascota['raza'])) $chips[] = $mascota['raza'];
                            if (!empty($mascota['color'])) $chips[] = $mascota['color'];
                            
                            $fechaEvento = !empty($mascota['fecha_estado']) ? $mascota['fecha_estado'] : ($mascota['fecha_perdida'] ?? null);
                            $fechaLegible = $fechaEvento ? date('d/m H:i', strtotime($fechaEvento)) : '';
                        ?>
                        <a class="pet-card" href="<?= BASE_URL ?>/mascota/<?= $mascota['id_mascota'] ?>" style="text-decoration:none; color:inherit; display:block;">
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
            <button class="btn-reportar" onclick="window.location.href='<?= BASE_URL ?>/registrar-mascota'">Reportar Mascota</button>
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
    <?php include ROOT_PATH . '/app/includes/bottom_nav.php'; ?>

    <script>
        let currentSlide = 0;
        const totalSlides = 4;
        const carousel = document.getElementById('carousel');
        const dots = document.querySelectorAll('.carousel-dot');

        function updateCarousel() {
            const offset = currentSlide * -100;
            carousel.style.transform = `translateX(${offset}%)`;
            
            // Actualizar dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function previousSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateCarousel();
        }

        // Auto-advance carousel cada 5 segundos
        setInterval(nextSlide, 5000);
    </script>
</body>
</html>

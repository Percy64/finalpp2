<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información QR - <?= htmlspecialchars($pet['nombre']) ?></title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/qr-info.css" />
</head>
<body>
    <div class="qr-container">
        <!-- Header dinámico según estado -->
        <?php 
            $estado_actual = $pet['estado'] ?? 'normal';
            $header_titulo = '';
            $header_subtitulo = '';
            $header_clase = '';
            
            if ($estado_actual === 'normal'):
                $header_titulo = '🐾 ¡Conoce a ' . htmlspecialchars($pet['nombre']) . '!';
                $header_subtitulo = 'Mascota feliz y segura en su hogar';
                $header_clase = 'header-normal';
            elseif ($estado_actual === 'perdida'):
                $header_titulo = '🚨 ¡MASCOTA PERDIDA!';
                $header_subtitulo = 'Por favor ayúdanos a encontrar a ' . htmlspecialchars($pet['nombre']);
                $header_clase = 'header-perdida';
            elseif ($estado_actual === 'encontrada'):
                $header_titulo = '✅ ¡MASCOTA ENCONTRADA!';
                $header_subtitulo = 'Hemos encontrado a ' . htmlspecialchars($pet['nombre']) . ' - Contacta al dueño';
                $header_clase = 'header-encontrada';
            endif;
        ?>
        <div class="qr-header <?= $header_clase ?>">
            <h1><?= $header_titulo ?></h1>
            <p><?= $header_subtitulo ?></p>
        </div>

        <!-- Foto -->
        <?php 
            $fotoUrl = !empty($pet['foto_url']) ? $pet['foto_url'] : ASSETS_URL . '/images/dog-placeholder.svg';
            if (!preg_match('/^https?:\/\//', $fotoUrl)) {
                $fotoUrl = BASE_URL . '/' . ltrim($fotoUrl, '/');
            }
        ?>
        <img src="<?= htmlspecialchars($fotoUrl) ?>" 
             class="pet-image"
             onerror="this.src='<?= ASSETS_URL ?>/images/dog-placeholder.svg'">

        <!-- Contenido -->
        <div class="qr-content">

            <!-- Estado de la mascota -->
            <?php 
                $estado_actual = $pet['estado'] ?? 'normal';
                $clase_estado = '';
                $texto_estado = '';
                
                if ($estado_actual === 'normal'):
                    $clase_estado = 'estado-normal';
                    $texto_estado = '👋 ¡Hola, soy ' . htmlspecialchars($pet['nombre']) . '!';
                elseif ($estado_actual === 'perdida'):
                    $clase_estado = 'estado-perdida';
                    $texto_estado = '🚨 MASCOTA PERDIDA';
                elseif ($estado_actual === 'encontrada'):
                    $clase_estado = 'estado-encontrada';
                    $texto_estado = '✅ MASCOTA ENCONTRADA';
                endif;
                
                if (!empty($texto_estado)):
            ?>
            <div class="estado-cartel <?= $clase_estado ?>">
                <?= $texto_estado ?>
            </div>
            <?php endif; ?>

            <!-- Información de la mascota -->
            <div class="pet-info">
                <p><strong>Especie:</strong> <?= htmlspecialchars(ucfirst($pet['especie'] ?? 'No especificada')) ?></p>
                <?php if (!empty($pet['raza'])): ?>
                <p><strong>Raza:</strong> <?= htmlspecialchars($pet['raza']) ?></p>
                <?php endif; ?>
                <?php if (!empty($pet['color'])): ?>
                <p><strong>Color:</strong> <?= htmlspecialchars($pet['color']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Sección de contacto -->
            <div class="contact-section">
                <div class="contact-title">📞 Contacta al dueño</div>
                <div class="contact-buttons">
                    <?php if (!empty($pet['telefono_dueno'])): ?>
                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $pet['telefono_dueno']) ?>?text=Encontr%C3%A9%20a%20tu%20mascota%20<?= htmlspecialchars($pet['nombre']) ?>" 
                       target="_blank" 
                       class="btn-contact btn-whatsapp">
                        <span>💬</span>
                        <span>WhatsApp</span>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($pet['telefono_dueno'])): ?>
                    <a href="tel:<?= htmlspecialchars($pet['telefono_dueno']) ?>" class="btn-contact btn-call">
                        <span>📱</span>
                        <span>Llamar</span>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($pet['email_dueno'])): ?>
                    <a href="mailto:<?= htmlspecialchars($pet['email_dueno']) ?>" class="btn-contact btn-email">
                        <span>✉️</span>
                        <span>Enviar Email</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información del dueño -->
            <div class="pet-info">
                <p><strong>Dueño:</strong> <?= htmlspecialchars($pet['nombre_dueno'] ?? 'No disponible') ?></p>
                <?php if (!empty($pet['telefono_dueno'])): ?>
                <p><strong>Teléfono:</strong> <a href="tel:<?= htmlspecialchars($pet['telefono_dueno']) ?>" style="color: #667eea; text-decoration: none;"><?= htmlspecialchars($pet['telefono_dueno']) ?></a></p>
                <?php endif; ?>
                <?php if (!empty($pet['email_dueno'])): ?>
                <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($pet['email_dueno']) ?>" style="color: #667eea; text-decoration: none;"><?= htmlspecialchars($pet['email_dueno']) ?></a></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="qr-footer">
            <p>¡Gracias por ayudar! 🙏</p>
        </div>
    </div>
</body>
</html>

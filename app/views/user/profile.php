<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Pet Alert</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mascota03.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/user-profile.css" />
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css" />
</head>
<body>
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
                        $avatarUrl = !empty($user['foto_url']) ? $user['foto_url'] : ASSETS_URL . '/images/usuarios/default-avatar.png';
                        if (!preg_match('/^https?:\/\//', $avatarUrl)) {
                            $avatarUrl = BASE_URL . '/' . ltrim($avatarUrl, '/');
                        }
                    ?>
                    <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Foto de perfil" class="avatar-img-header"
                         onerror="this.src='<?= ASSETS_URL ?>/images/usuarios/default-avatar.png'">
                </div>
            </div>

            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="success-message" style="background-color:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px;">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_message'])): ?>
                <div class="error-message">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <!-- Información del usuario -->
            <div class="user-info">
                <div class="user-details-center">
                    <h3><?= htmlspecialchars($user['nombre']) ?></h3>
                    <p class="user-email"><?= htmlspecialchars($user['email']) ?></p>
                    <?php if (!empty($user['telefono'])): ?>
                        <p class="user-phone">📞 <?= htmlspecialchars($user['telefono']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user['direccion'])): ?>
                        <p class="user-address">📍 <?= htmlspecialchars($user['direccion']) ?></p>
                    <?php endif; ?>
                    <p class="user-since">Miembro desde: <?= date('d/m/Y', strtotime($user['fecha_registro'] ?? $user['fecha_creacion'] ?? 'now')) ?></p>
                    
                    <!-- Botones de acción -->
                    <?php if ($isOwnProfile): ?>
                        <div class="edit-profile-section">
                            <button onclick="window.location.href='<?= BASE_URL ?>/editar-perfil'" class="btn-edit-profile">
                                ✏️ Editar perfil
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sección de mascotas -->
            <div class="mascotas-section">
                <div class="section-header">
                    <h3><?= $isOwnProfile ? 'Mis Mascotas' : 'Sus Mascotas' ?></h3>
                    <?php if ($isOwnProfile): ?>
                        <button onclick="window.location.href='<?= BASE_URL ?>/registrar-mascota'" class="btn-add-pet">➕ Agregar</button>
                    <?php endif; ?>
                </div>

                <?php if (empty($pets)): ?>
                    <p class="no-mascotas">
                        <?= $isOwnProfile ? 'Aún no has registrado ninguna mascota.' : 'Este usuario no tiene mascotas registradas.' ?>
                    </p>
                <?php else: ?>
                    <div class="mascotas-grid">
                        <?php foreach ($pets as $mascota): ?>
                            <?php
                                $fotoUrl = !empty($mascota['foto_url']) ? $mascota['foto_url'] : ASSETS_URL . '/images/dog-placeholder.svg';
                                if (!preg_match('/^https?:\/\//', $fotoUrl)) {
                                    $fotoUrl = BASE_URL . '/' . ltrim($fotoUrl, '/');
                                }
                            ?>
                            <div class="mascota-card">
                                <div class="mascota-image-container">
                                    <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="<?= htmlspecialchars($mascota['nombre']) ?>" class="mascota-image"
                                         onerror="this.src='<?= ASSETS_URL ?>/images/dog-placeholder.svg'">
                                    <?php if (!empty($mascota['estado']) && $mascota['estado'] !== 'normal'): ?>
                                        <span class="mascota-status status-<?= htmlspecialchars($mascota['estado']) ?>">
                                            <?= $mascota['estado'] === 'perdida' ? '🔍 Perdida' : '✅ Encontrada' ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="mascota-info">
                                    <h4><?= htmlspecialchars($mascota['nombre']) ?></h4>
                                    <p><?= htmlspecialchars($mascota['especie']) ?><?= !empty($mascota['raza']) ? ' • ' . htmlspecialchars($mascota['raza']) : '' ?></p>
                                    <?php if (!empty($mascota['color'])): ?>
                                        <p class="mascota-color">Color: <?= htmlspecialchars($mascota['color']) ?></p>
                                    <?php endif; ?>
                                    <div class="mascota-actions">
                                        <button onclick="window.location.href='<?= BASE_URL ?>/mascota/<?= $mascota['id_mascota'] ?>'" class="btn-ver">Ver</button>
                                        <?php if ($isOwnProfile): ?>
                                            <button onclick="window.location.href='<?= BASE_URL ?>/mascota/<?= $mascota['id_mascota'] ?>/editar'" class="btn-editar">Editar</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botón de cerrar sesión al final -->
        <?php if ($isOwnProfile): ?>
        <div class="logout-section">
            <button onclick="if(confirm('¿Estás seguro de que deseas cerrar sesión?')) window.location.href='<?= BASE_URL ?>/logout'" class="btn-logout-main">
                🚪 Cerrar sesión
            </button>
        </div>
        <?php endif; ?>
    </section>

    <!-- Barra de navegación inferior -->
    <?php include ROOT_PATH . '/app/includes/bottom_nav.php'; ?>
</body>
</html>

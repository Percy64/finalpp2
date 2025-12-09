<?php
require_once __DIR__ . '/config.php';
// No iniciar sesión aquí para evitar "headers already sent" si este archivo se incluye después de enviar HTML
// Solo detectamos si hay un usuario logueado de forma segura
$usuario_logueado = !empty($_SESSION['usuario_id']);
?>
<div class="bottom-nav">
    <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>/home/index.php'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
    </button>
    <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>/mascota/mapa.php'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
    </button>
    <button class="nav-btn" onclick="verificarLogin()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
    </button>
    <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>/legal/mision_vision.php'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
    </button>
</div>
<script>
function verificarLogin() {
    <?php if ($usuario_logueado): ?>
    window.location.href = '<?= BASE_URL ?>/usuario/perfil_usuario.php';
    <?php else: ?>
    window.location.href = '<?= BASE_URL ?>/usuario/iniciosesion.php';
    <?php endif; ?>
}
</script>

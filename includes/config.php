<?php
// Base URL for building absolute links (adjust if your local URL changes)
if (!defined('BASE_URL')) {
    // Try to detect base path automatically; fallback to '/lost-found'
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    // Heuristic: assume app folder name is 'lost-found'
    $defaultBase = '/lost-found';
    $pos = strpos($scriptName, '/lost-found/');
    if ($pos !== false) {
        $detectedBase = substr($scriptName, 0, $pos + strlen('/lost-found'));
    } else {
        $detectedBase = $defaultBase;
    }
    define('BASE_URL', $detectedBase);
}

if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', BASE_URL . '/assets');
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/..'));
}

// Best-effort public IPv4 detection for LAN QR links
if (!defined('PUBLIC_HOST')) {
    $ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
    // Prefer IPv4; if IPv6 loopback or empty, try hostname resolution
    if (!$ip || $ip === '::1' || $ip === '127.0.0.1') {
        $resolved = getHostByName(getHostName());
        if (filter_var($resolved, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $resolved !== '127.0.0.1') {
            $ip = $resolved;
        }
    }
    // Fallback to localhost if nothing better
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $ip = '127.0.0.1';
    }
    define('PUBLIC_HOST', $ip);
}

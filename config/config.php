<?php
/**
 * Application configuration
 */

// Detect base URL automatically
if (!defined('BASE_URL')) {
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $defaultBase = '/finalpp2';
    $pos = strpos($scriptName, '/finalpp2/');
    if ($pos !== false) {
        $detectedBase = substr($scriptName, 0, $pos + strlen('/finalpp2'));
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

if (!defined('PUBLIC_HOST')) {
    $ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
    if (!$ip || $ip === '::1' || $ip === '127.0.0.1') {
        $resolved = getHostByName(getHostName());
        if (filter_var($resolved, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $resolved !== '127.0.0.1') {
            $ip = $resolved;
        }
    }
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $ip = '127.0.0.1';
    }
    define('PUBLIC_HOST', $ip);
}

// App paths
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/views');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('MODEL_PATH', APP_PATH . '/models');

<?php
/**
 * PALMED Clinic - Application Bootstrap
 */

declare(strict_types=1);

define('PALMED_ROOT', dirname(__DIR__));
define('PALMED_APP', PALMED_ROOT . '/app');
define('PALMED_CONFIG', PALMED_ROOT . '/config');
define('PALMED_VIEWS', PALMED_ROOT . '/views');
define('PALMED_UPLOADS', PALMED_ROOT . '/uploads');
define('PALMED_ASSETS', PALMED_ROOT . '/assets');

$configFile = PALMED_CONFIG . '/config.php';

if (!file_exists($configFile)) {
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/install') === false) {
        header('Location: /install/');
        exit;
    }

    $config = require PALMED_CONFIG . '/config.sample.php';
} else {
    $config = require $configFile;
}

date_default_timezone_set($config['timezone'] ?? 'America/Bogota');

spl_autoload_register(function (string $class): void {
    $prefix = 'Palmed\\';
    $baseDir = PALMED_APP . '/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require_once PALMED_APP . '/Helpers/functions.php';

<?php
/**
 * PALMED Clinic - Front Controller
 * PALMED Health Group S.A.S.
 */

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

use Palmed\Core\Auth;
use Palmed\Core\Database;
use Palmed\Core\Session;

// Redirect to installer if not installed
if (!config('installed', false) && !file_exists(PALMED_CONFIG . '/config.php')) {
    header('Location: ' . (rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/') ?: '') . '/install/');
    exit;
}

Session::start();

// Connect to database if installed
if (config('installed', false) || file_exists(PALMED_CONFIG . '/config.php')) {
    try {
        Database::getInstance();
    } catch (Throwable $e) {
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/install') === false) {
            die('<h2>Error de base de datos</h2><p>' . htmlspecialchars($e->getMessage()) . '</p>');
        }
    }
}

/** @var \Palmed\Core\Router $router */
$router = require PALMED_APP . '/routes.php';

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$uri = $_SERVER['REQUEST_URI'] ?? '/';
if ($basePath && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath)) ?: '/';
}
$uri = strtok($uri, '?') ?: '/';

$router->dispatch($uri, $_SERVER['REQUEST_METHOD'] ?? 'GET');

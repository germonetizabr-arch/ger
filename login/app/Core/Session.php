<?php

declare(strict_types=1);

namespace Palmed\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $sessionName = config('session.name', 'PALMED_SESSION');
        $lifetime = config('session.lifetime', 7200);

        session_name($sessionName);
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }
}

<?php

declare(strict_types=1);

namespace Palmed\Core;

use PDO;

class Auth
{
public static function attempt(string $email, string $password, bool $remember = false): bool
{
    $db = Database::getInstance();

    $stmt = $db->prepare(
        'SELECT u.*, r.slug AS role_slug, r.name AS role_name
         FROM users u
         JOIN roles r ON r.id = u.role_id
         WHERE (u.email = ? OR u.username = ?)
         AND u.is_active = 1
         LIMIT 1'
    );

    $stmt->execute([$email, $email]);

    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return false;
    }

    self::loginUser($user);

    $db->prepare(
        'UPDATE users SET last_login_at = NOW() WHERE id = ?'
    )->execute([$user['id']]);

    if ($remember) {
        self::setRememberToken((int) $user['id']);
    }

    Audit::log(
        'login',
        'user',
        (int) $user['id'],
        'User logged in',
        (int) $user['id']
    );

    return true;
$db = Database::getInstance();

    $stmt = $db->prepare(
        'SELECT u.*, r.slug AS role_slug, r.name AS role_name
         FROM users u
         JOIN roles r ON r.id = u.role_id
         WHERE (u.email = ? OR u.username = ?)
         AND u.is_active = 1
         LIMIT 1'
    );

    $stmt->execute([$email, $email]);

    $user = $stmt->fetch();

    $user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    return false;

self::loginUser($user);
    
    }

    self::loginUser($user);

    $db->prepare(
        'UPDATE users SET last_login_at = NOW() WHERE id = ?'
    )->execute([$user['id']]);

    if ($remember) {
        self::setRememberToken((int)$user['id']);
    }

    Audit::log(
        'login',
        'user',
        (int)$user['id'],
        'User logged in',
        (int)$user['id']
    );

    return true;
}

    public static function loginUser(array $user): void
    {
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
        $_SESSION['role_id'] = (int) $user['role_id'];
        $_SESSION['role_slug'] = $user['role_slug'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['language'] = $user['language'] ?? config('default_language', 'es');
        $_SESSION['permissions'] = self::loadPermissions((int) $user['role_id']);
    }

    public static function checkRememberToken(): bool
    {
        $cookieName = config('session.name', 'PALMED_SESSION') . '_remember';
        if (empty($_COOKIE[$cookieName])) {
            return false;
        }

        $token = $_COOKIE[$cookieName];
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT u.*, r.slug AS role_slug, r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.remember_token = ? AND u.is_active = 1
             LIMIT 1'
        );
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }

        self::loginUser($user);
        return true;
    }

    private static function setRememberToken(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $db = Database::getInstance();
        $db->prepare('UPDATE users SET remember_token = ? WHERE id = ?')->execute([$token, $userId]);

        $cookieName = config('session.name', 'PALMED_SESSION') . '_remember';
        $days = config('session.remember_days', 30);
        setcookie($cookieName, $token, [
            'expires' => time() + ($days * 86400),
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public static function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            Audit::log('logout', 'user', (int) $userId, 'User logged out', (int) $userId);
            $db = Database::getInstance();
            $db->prepare('UPDATE users SET remember_token = NULL WHERE id = ?')->execute([$userId]);
        }

        $cookieName = config('session.name', 'PALMED_SESSION') . '_remember';
        setcookie($cookieName, '', ['expires' => time() - 3600, 'path' => '/']);

        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT u.*, r.slug AS role_slug, r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = ?
             LIMIT 1'
        );
        $stmt->execute([self::id()]);
        return $stmt->fetch() ?: null;
    }

    public static function can(string $permission): bool
    {
        if (($_SESSION['role_slug'] ?? '') === 'super_admin') {
            return true;
        }
        return in_array($permission, $_SESSION['permissions'] ?? [], true);
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            if (self::checkRememberToken()) {
                return;
            }
            set_flash('error', __t('auth.login_required'));
            redirect('login');
        }
    }

    public static function requirePermission(string $permission): void
    {
        self::requireAuth();
        if (!self::can($permission)) {
            set_flash('error', __t('auth.access_denied'));
            redirect('dashboard');
        }
    }

    private static function loadPermissions(int $roleId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.slug FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = ?'
        );
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
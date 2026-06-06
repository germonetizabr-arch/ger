<?php

declare(strict_types=1);

namespace Palmed\Controllers;

use Palmed\Core\Auth;
use Palmed\Core\Audit;
use Palmed\Core\Database;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('dashboard');
        }
        view('auth.login');
    }

    public function login(): void
    {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', __t('auth.invalid_token'));
            redirect('login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = !empty($_POST['remember']);

        if (empty($email) || empty($password)) {
            set_flash('error', __t('auth.credentials_required'));
            set_old_input(['email' => $email]);
            redirect('login');
        }

        if (!Auth::attempt($email, $password, $remember)) {
            set_flash('error', __t('auth.invalid_credentials'));
            set_old_input(['email' => $email]);
            redirect('login');
        }

        clear_old_input();
        redirect('dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('login');
    }

    public function showForgotPassword(): void
    {
        view('auth.forgot-password');
    }

    public function forgotPassword(): void
    {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', __t('auth.invalid_token'));
            redirect('forgot-password');
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            set_flash('error', __t('auth.email_required'));
            redirect('forgot-password');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $db->prepare(
                'UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?'
            )->execute([$token, $expires, $user['id']]);

            Audit::log('password_reset_request', 'user', (int) $user['id'], 'Password reset requested');
        }

        set_flash('success', __t('auth.reset_email_sent'));
        redirect('login');
    }
}

<?php

declare(strict_types=1);

namespace Palmed\Controllers;

use Palmed\Core\Auth;
use Palmed\Models\User;

class UserController
{
    public function index(): void
    {
        Auth::requireAuth();

        view('users.index', [
            'users' => User::all()
        ]);
    }

    public function create(): void
    {
        Auth::requireAuth();

        view('users.form', [
            'roles' => User::roles()
        ]);
    }

    public function store(): void
    {
        Auth::requireAuth();

        User::create([
            'role_id' => (int) ($_POST['role_id'] ?? 0),
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        redirect('users');
    }

    public function edit(array $params): void
    {
        Auth::requireAuth();

        $user = User::find((int) $params['id']);

        if (!$user) {
            redirect('users');
        }

        view('users.form', [
            'user' => $user,
            'roles' => User::roles()
        ]);
    }

    public function update(array $params): void
    {
        Auth::requireAuth();

        $id = (int) $params['id'];

        $user = User::find($id);

        if (!$user) {
            redirect('users');
        }

        $signatureFile = $user['signature_file'] ?? null;
        $signaturePath = $user['signature_path'] ?? null;

        if (
            isset($_FILES['signature']) &&
            $_FILES['signature']['error'] === UPLOAD_ERR_OK
        ) {

            $extension = strtolower(
                pathinfo(
                    $_FILES['signature']['name'],
                    PATHINFO_EXTENSION
                )
            );

            if (in_array($extension, ['png', 'jpg', 'jpeg'])) {

                $fileName =
                    'signature_' .
                    $id .
                    '_' .
                    time() .
                    '.' .
                    $extension;

                $relativePath =
                    'uploads/signatures/' .
                    $fileName;

                $absolutePath =
                    PALMED_ROOT .
                    '/' .
                    $relativePath;

                move_uploaded_file(
                    $_FILES['signature']['tmp_name'],
                    $absolutePath
                );

                $signatureFile = $fileName;
                $signaturePath = $relativePath;
            }
        }

        User::update(
            $id,
            [
                'role_id' => (int) ($_POST['role_id'] ?? 0),
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'is_active' => (int) ($_POST['is_active'] ?? 1),
                'signature_file' => $signatureFile,
                'signature_path' => $signaturePath,
            ]
        );

        redirect('users/' . $id . '/edit');
    }
}
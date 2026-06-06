<?php

declare(strict_types=1);

namespace Palmed\Models;

use Palmed\Core\Database;

class User
{
    public static function all(): array
    {
        $db = Database::getInstance();

        $stmt = $db->query("
            SELECT
                u.*,
                r.name AS role_name
            FROM users u
            LEFT JOIN roles r ON r.id = u.role_id
            ORDER BY u.first_name, u.last_name
        ");

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT *
            FROM users
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public static function roles(): array
    {
        $db = Database::getInstance();

        $stmt = $db->query("
            SELECT *
            FROM roles
            ORDER BY name
        ");

        return $stmt->fetchAll();
    }

    public static function create(array $data): bool
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            INSERT INTO users (
                role_id,
                username,
                email,
                password,
                first_name,
                last_name,
                is_active
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?
            )
        ");

        return $stmt->execute([
            $data['role_id'],
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['first_name'],
            $data['last_name'],
            $data['is_active']
        ]);
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();

        // Actualizar contraseña si se envió una nueva
        if (!empty($data['password'])) {

            $stmt = $db->prepare("
                UPDATE users
                SET
                    role_id = ?,
                    username = ?,
                    email = ?,
                    password = ?,
                    first_name = ?,
                    last_name = ?,
                    is_active = ?,
                    signature_file = ?,
                    signature_path = ?
                WHERE id = ?
            ");

            return $stmt->execute([
                $data['role_id'],
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['first_name'],
                $data['last_name'],
                $data['is_active'],
                $data['signature_file'] ?? null,
                $data['signature_path'] ?? null,
                $id
            ]);
        }

        // Mantener contraseña actual
        $stmt = $db->prepare("
            UPDATE users
            SET
                role_id = ?,
                username = ?,
                email = ?,
                first_name = ?,
                last_name = ?,
                is_active = ?,
                signature_file = ?,
                signature_path = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['role_id'],
            $data['username'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['is_active'],
            $data['signature_file'] ?? null,
            $data['signature_path'] ?? null,
            $id
        ]);
    }
}
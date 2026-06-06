<?php

declare(strict_types=1);

namespace Palmed\Core;

use PDO;

class Audit
{
    public static function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        ?int $userId = null
    ): void {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $userId ?? ($_SESSION['user_id'] ?? null),
                $action,
                $entityType,
                $entityId,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            ]);
        } catch (\Throwable) {
            // Audit failure should not break application flow
        }
    }
}

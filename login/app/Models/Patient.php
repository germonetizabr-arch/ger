<?php

declare(strict_types=1);

namespace Palmed\Models;

use Palmed\Core\Database;
use PDO;

class Patient
{
    public static function all(int $limit = 50, int $offset = 0, ?string $search = null): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT * FROM patients WHERE is_active = 1';
        $params = [];

        if ($search) {
            $sql .= ' AND (
                first_name LIKE ? OR last_name LIKE ? OR
                document_number LIKE ? OR phone LIKE ? OR email LIKE ? OR
                CONCAT(first_name, " ", last_name) LIKE ?
            )';
            $term = '%' . $search . '%';
            $params = array_fill(0, 6, $term);
        }

        $sql .= ' ORDER BY last_name, first_name LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count(?string $search = null): int
    {
        $db = Database::getInstance();
        $sql = 'SELECT COUNT(*) FROM patients WHERE is_active = 1';
        $params = [];

        if ($search) {
            $sql .= ' AND (
                first_name LIKE ? OR last_name LIKE ? OR
                document_number LIKE ? OR phone LIKE ? OR email LIKE ? OR
                CONCAT(first_name, " ", last_name) LIKE ?
            )';
            $term = '%' . $search . '%';
            $params = array_fill(0, 6, $term);
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM patients WHERE id = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO patients (
                first_name, last_name, document_type, document_number,
                date_of_birth, age, sex, phone, email, address, occupation,
                emergency_contact_name, emergency_contact_phone, notes, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $age = $data['age'] ?? calculate_age($data['date_of_birth'] ?? null);

        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['document_type'] ?? 'CC',
            $data['document_number'],
            $data['date_of_birth'] ?: null,
            $age,
            $data['sex'] ?? 'O',
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['occupation'] ?? null,
            $data['emergency_contact_name'] ?? null,
            $data['emergency_contact_phone'] ?? null,
            $data['notes'] ?? null,
            $data['created_by'] ?? null,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $age = $data['age'] ?? calculate_age($data['date_of_birth'] ?? null);

        $stmt = $db->prepare(
            'UPDATE patients SET
                first_name = ?, last_name = ?, document_type = ?, document_number = ?,
                date_of_birth = ?, age = ?, sex = ?, phone = ?, email = ?,
                address = ?, occupation = ?, emergency_contact_name = ?,
                emergency_contact_phone = ?, notes = ?, updated_at = NOW()
             WHERE id = ? AND is_active = 1'
        );

        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['document_type'] ?? 'CC',
            $data['document_number'],
            $data['date_of_birth'] ?: null,
            $age,
            $data['sex'] ?? 'O',
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['occupation'] ?? null,
            $data['emergency_contact_name'] ?? null,
            $data['emergency_contact_phone'] ?? null,
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public static function recent(int $limit = 5): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT * FROM patients WHERE is_active = 1 ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function getConsultations(int $patientId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT c.*, u.first_name AS physician_first, u.last_name AS physician_last,
                    s.name AS specialty_name
             FROM consultations c
             JOIN users u ON u.id = c.physician_id
             LEFT JOIN specialties s ON s.id = c.specialty_id
             WHERE c.patient_id = ?
             ORDER BY c.consultation_date DESC'
        );
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public static function getDocuments(int $patientId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT d.*, u.first_name AS uploader_first, u.last_name AS uploader_last
             FROM documents d
             LEFT JOIN users u ON u.id = d.uploaded_by
             WHERE d.patient_id = ?
             ORDER BY d.created_at DESC'
        );
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }
}

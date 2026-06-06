<?php

declare(strict_types=1);

namespace Palmed\Models;

use Palmed\Core\Database;

class Dashboard
{
    public static function todayAppointments(?int $physicianId = null): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT a.*, p.first_name AS patient_first, p.last_name AS patient_last,
                       u.first_name AS physician_first, u.last_name AS physician_last
                FROM appointments a
                JOIN patients p ON p.id = a.patient_id
                JOIN users u ON u.id = a.physician_id
                WHERE a.appointment_date = CURDATE()
                AND a.status NOT IN ("cancelled")';
        $params = [];

        if ($physicianId) {
            $sql .= ' AND a.physician_id = ?';
            $params[] = $physicianId;
        }

        $sql .= ' ORDER BY a.start_time ASC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function upcomingAppointments(?int $physicianId = null, int $limit = 5): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT a.*, p.first_name AS patient_first, p.last_name AS patient_last
                FROM appointments a
                JOIN patients p ON p.id = a.patient_id
                WHERE a.appointment_date > CURDATE()
                AND a.status IN ("scheduled", "confirmed")';
        $params = [];

        if ($physicianId) {
            $sql .= ' AND a.physician_id = ?';
            $params[] = $physicianId;
        }

        $sql .= ' ORDER BY a.appointment_date ASC, a.start_time ASC LIMIT ?';
        $params[] = $limit;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function pendingDocumentsCount(): int
    {
        $db = Database::getInstance();
        return (int) $db->query(
            'SELECT COUNT(*) FROM consultations WHERE status = "draft"'
        )->fetchColumn();
    }

    public static function stats(?int $physicianId = null): array
    {
        return [
            'today_appointments' => count(self::todayAppointments($physicianId)),
            'today_consultations' => Consultation::todayCount($physicianId),
            'pending_documents' => self::pendingDocumentsCount(),
            'total_patients' => (int) Database::getInstance()->query(
                'SELECT COUNT(*) FROM patients WHERE is_active = 1'
            )->fetchColumn(),
        ];
    }
}

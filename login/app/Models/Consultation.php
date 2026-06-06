<?php

declare(strict_types=1);

namespace Palmed\Models;

use Palmed\Core\Database;
use PDO;

class Consultation
{
public static function find(int $id): ?array
{
    $db = Database::getInstance();

    $stmt = $db->prepare(
        'SELECT
            c.*,
            p.first_name AS patient_first,
            p.last_name AS patient_last,
            p.document_type,
            p.document_number,
            p.date_of_birth,
            p.sex,
            p.age,

            u.first_name AS physician_first,
            u.last_name AS physician_last,
            u.professional_license,
            u.signature_path,

            s.name AS specialty_name

        FROM consultations c

        INNER JOIN patients p
            ON p.id = c.patient_id

        INNER JOIN users u
            ON u.id = c.physician_id

        LEFT JOIN specialties s
            ON s.id = c.specialty_id

        WHERE c.id = ?

        LIMIT 1'
    );

   $stmt->execute([$id]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

return $row ?: null;

    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $bmi = calculate_bmi(
            isset($data['weight']) ? (float) $data['weight'] : null,
            isset($data['height']) ? (float) $data['height'] : null
        );

        $stmt = $db->prepare(
            'INSERT INTO consultations (
                patient_id, physician_id, appointment_id, specialty_id, consultation_date, status,
                reason_for_consultation, current_illness, past_medical_history, surgical_history,
                family_history, allergies, current_medications,
                blood_pressure_systolic, blood_pressure_diastolic, heart_rate, respiratory_rate,
                temperature, weight, height, bmi, physical_examination, assessment,
                management_plan, medications_prescribed, medical_orders, recommendations,
                follow_up_plan, medical_leave_days, medical_leave_from, medical_leave_to,
                medical_leave_reason, digital_signature, signed_at, created_by, updated_by
            ) VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )'
        );

        $stmt->execute([
            $data['patient_id'],
            $data['physician_id'],
            $data['appointment_id'] ?? null,
            $data['specialty_id'] ?? null,
            $data['consultation_date'] ?? date('Y-m-d H:i:s'),
            $data['status'] ?? 'draft',
            $data['reason_for_consultation'] ?? null,
            $data['current_illness'] ?? null,
            $data['past_medical_history'] ?? null,
            $data['surgical_history'] ?? null,
            $data['family_history'] ?? null,
            $data['allergies'] ?? null,
            $data['current_medications'] ?? null,
            $data['blood_pressure_systolic'] ?: null,
            $data['blood_pressure_diastolic'] ?: null,
            $data['heart_rate'] ?: null,
            $data['respiratory_rate'] ?: null,
            $data['temperature'] ?: null,
            $data['weight'] ?: null,
            $data['height'] ?: null,
            $bmi,
            $data['physical_examination'] ?? null,
            $data['assessment'] ?? null,
            $data['management_plan'] ?? null,
            $data['medications_prescribed'] ?? null,
            $data['medical_orders'] ?? null,
            $data['recommendations'] ?? null,
            $data['follow_up_plan'] ?? null,
            $data['medical_leave_days'] ?: null,
            $data['medical_leave_from'] ?: null,
            $data['medical_leave_to'] ?: null,
            $data['medical_leave_reason'] ?? null,
            $data['digital_signature'] ?? null,
            !empty($data['digital_signature']) ? date('Y-m-d H:i:s') : null,
            $data['created_by'] ?? null,
            $data['updated_by'] ?? null,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $bmi = calculate_bmi(
            isset($data['weight']) ? (float) $data['weight'] : null,
            isset($data['height']) ? (float) $data['height'] : null
        );

        $stmt = $db->prepare(
            'UPDATE consultations SET
                specialty_id = ?, status = ?,
                reason_for_consultation = ?, current_illness = ?, past_medical_history = ?,
                surgical_history = ?, family_history = ?, allergies = ?, current_medications = ?,
                blood_pressure_systolic = ?, blood_pressure_diastolic = ?, heart_rate = ?,
                respiratory_rate = ?, temperature = ?, weight = ?, height = ?, bmi = ?,
                physical_examination = ?, assessment = ?, management_plan = ?,
                medications_prescribed = ?, medical_orders = ?, recommendations = ?,
                follow_up_plan = ?, medical_leave_days = ?, medical_leave_from = ?,
                medical_leave_to = ?, medical_leave_reason = ?, digital_signature = ?,
                signed_at = ?, updated_by = ?, updated_at = NOW()
             WHERE id = ?'
        );

        return $stmt->execute([
            $data['specialty_id'] ?? null,
            $data['status'] ?? 'draft',
            $data['reason_for_consultation'] ?? null,
            $data['current_illness'] ?? null,
            $data['past_medical_history'] ?? null,
            $data['surgical_history'] ?? null,
            $data['family_history'] ?? null,
            $data['allergies'] ?? null,
            $data['current_medications'] ?? null,
            $data['blood_pressure_systolic'] ?: null,
            $data['blood_pressure_diastolic'] ?: null,
            $data['heart_rate'] ?: null,
            $data['respiratory_rate'] ?: null,
            $data['temperature'] ?: null,
            $data['weight'] ?: null,
            $data['height'] ?: null,
            $bmi,
            $data['physical_examination'] ?? null,
            $data['assessment'] ?? null,
            $data['management_plan'] ?? null,
            $data['medications_prescribed'] ?? null,
            $data['medical_orders'] ?? null,
            $data['recommendations'] ?? null,
            $data['follow_up_plan'] ?? null,
            $data['medical_leave_days'] ?: null,
            $data['medical_leave_from'] ?: null,
            $data['medical_leave_to'] ?: null,
            $data['medical_leave_reason'] ?? null,
            $data['digital_signature'] ?? null,
            !empty($data['digital_signature']) ? date('Y-m-d H:i:s') : null,
            $data['updated_by'] ?? null,
            $id,
        ]);
    }

    public static function saveDiagnoses(int $consultationId, array $diagnoses): void
    {
        $db = Database::getInstance();
        $db->prepare('DELETE FROM consultation_diagnoses WHERE consultation_id = ?')->execute([$consultationId]);

        if (empty($diagnoses)) {
            return;
        }

        $stmt = $db->prepare(
            'INSERT INTO consultation_diagnoses (consultation_id, icd10_id, icd10_code, description, is_primary, sort_order)
             VALUES (?, ?, ?, ?, ?, ?)'
        );

        foreach ($diagnoses as $i => $dx) {
            if (empty($dx['description'])) {
                continue;
            }
            $stmt->execute([
                $consultationId,
                $dx['icd10_id'] ?? null,
                $dx['icd10_code'] ?? null,
                $dx['description'],
                !empty($dx['is_primary']) ? 1 : 0,
                $i,
            ]);
        }
    }

    public static function getDiagnoses(int $consultationId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT * FROM consultation_diagnoses WHERE consultation_id = ? ORDER BY sort_order'
        );
        $stmt->execute([$consultationId]);
        return $stmt->fetchAll();
    }

    public static function todayCount(?int $physicianId = null): int
    {
        $db = Database::getInstance();
        $sql = 'SELECT COUNT(*) FROM consultations WHERE DATE(consultation_date) = CURDATE()';
        $params = [];

        if ($physicianId) {
            $sql .= ' AND physician_id = ?';
            $params[] = $physicianId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function searchIcd10(string $query, string $lang = 'es', int $limit = 20): array
    {
        $db = Database::getInstance();
        $col = $lang === 'en' ? 'description_en' : 'description_es';
        $stmt = $db->prepare(
            "SELECT id, code, description_es, description_en
             FROM icd10_codes
             WHERE is_active = 1 AND (code LIKE ? OR description_es LIKE ? OR description_en LIKE ?)
             ORDER BY code LIMIT ?"
        );
        $term = '%' . $query . '%';
        $stmt->execute([$term, $term, $term, $limit]);
        return $stmt->fetchAll();
    }
}

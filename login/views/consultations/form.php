<?php
$isEdit = $action === 'edit';
$c = $consultation ?? [];
$pageTitle = $isEdit ? __t('consultations.edit') : __t('consultations.new');
$activeNav = 'consultations';
$extraScripts = ['consultation.js'];
$formAction = $isEdit ? url('consultations/' . $c['id']) : url('consultations');
ob_start();
?>
<div class="top-bar">
    <h1 class="page-title"><?= e($pageTitle) ?></h1>
    <?php if ($patient): ?>
    <span class="badge bg-primary fs-6"><?= e($patient['first_name'] . ' ' . $patient['last_name']) ?></span>
    <?php endif; ?>
</div>

<form id="consultation-form" method="POST" action="<?= $formAction ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="patient_id" id="patient_id" value="<?= (int) ($c['patient_id'] ?? $patient['id'] ?? 0) ?>">

    <?php if (!$patient && !$isEdit): ?>
    <div class="card mb-4">
        <div class="card-body">
            <label class="form-label"><?= e(__t('consultations.select_patient')) ?> *</label>
            <div class="position-relative">
                <input type="text" id="patient_search" class="form-control" placeholder="<?= e(__t('patients.search_placeholder')) ?>" autocomplete="off">
                <div id="patient_search_results" class="patient-search-results"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
<div class="row g-3 mb-3">
<div class="col-md-4">

    <label class="form-label">
        <?= e(__t('consultations.specialty')) ?>
    </label>

    <?php
    $userSpecialty = $physicianSpecialties[0] ?? null;
    ?>

    <input
        type="text"
        class="form-control"
        value="<?= e($userSpecialty['name'] ?? 'Sin especialidad asignada') ?>"
        readonly>

    <input
        type="hidden"
        name="specialty_id"
        value="<?= (int)($userSpecialty['id'] ?? 0) ?>">

</div>
        <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select name="status" class="form-select">
                <option value="draft" <?= ($c['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>><?= e(__t('consultations.status_draft')) ?></option>
                <option value="completed" <?= ($c['status'] ?? '') === 'completed' ? 'selected' : '' ?>><?= e(__t('consultations.status_completed')) ?></option>
            </select>
        </div>
    </div>

    <!-- Section 1: Reason -->
    <div class="consultation-section">
        <div class="section-header">1. <?= e(__t('consultations.reason')) ?></div>
        <div class="section-body">
            <textarea name="reason_for_consultation" class="form-control" rows="3"><?= e($c['reason_for_consultation'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Section 2: Current Illness -->
    <div class="consultation-section">
        <div class="section-header">2. <?= e(__t('consultations.current_illness')) ?></div>
        <div class="section-body">
            <textarea name="current_illness" class="form-control" rows="4"><?= e($c['current_illness'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Sections 3-7: History -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="consultation-section h-100">
                <div class="section-header">3. <?= e(__t('consultations.past_history')) ?></div>
                <div class="section-body">
                    <textarea name="past_medical_history" class="form-control" rows="4"><?= e($c['past_medical_history'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="consultation-section h-100">
                <div class="section-header">4. <?= e(__t('consultations.surgical_history')) ?></div>
                <div class="section-body">
                    <textarea name="surgical_history" class="form-control" rows="4"><?= e($c['surgical_history'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="consultation-section h-100">
                <div class="section-header">5. <?= e(__t('consultations.family_history')) ?></div>
                <div class="section-body">
                    <textarea name="family_history" class="form-control" rows="3"><?= e($c['family_history'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="consultation-section h-100">
                <div class="section-header">6. <?= e(__t('consultations.allergies')) ?></div>
                <div class="section-body">
                    <textarea name="allergies" class="form-control" rows="3"><?= e($c['allergies'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="consultation-section">
        <div class="section-header">7. <?= e(__t('consultations.current_medications')) ?></div>
        <div class="section-body">
            <textarea name="current_medications" class="form-control" rows="3"><?= e($c['current_medications'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Section 8: Vital Signs -->
    <div class="consultation-section">
        <div class="section-header">8. <?= e(__t('consultations.vital_signs')) ?></div>
        <div class="section-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label"><?= e(__t('consultations.blood_pressure')) ?> (Sys)</label>
                    <input type="number" name="blood_pressure_systolic" class="form-control" value="<?= e((string)($c['blood_pressure_systolic'] ?? '')) ?>" min="0" max="300">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><?= e(__t('consultations.blood_pressure')) ?> (Dia)</label>
                    <input type="number" name="blood_pressure_diastolic" class="form-control" value="<?= e((string)($c['blood_pressure_diastolic'] ?? '')) ?>" min="0" max="200">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><?= e(__t('consultations.heart_rate')) ?></label>
                    <input type="number" name="heart_rate" class="form-control" value="<?= e((string)($c['heart_rate'] ?? '')) ?>" min="0" max="300">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><?= e(__t('consultations.respiratory_rate')) ?></label>
                    <input type="number" name="respiratory_rate" class="form-control" value="<?= e((string)($c['respiratory_rate'] ?? '')) ?>" min="0" max="100">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><?= e(__t('consultations.temperature')) ?> °C</label>
                    <input type="number" step="0.1" name="temperature" class="form-control" value="<?= e((string)($c['temperature'] ?? '')) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><?= e(__t('consultations.weight')) ?></label>
                    <input type="number" step="0.1" name="weight" id="weight" class="form-control" value="<?= e((string)($c['weight'] ?? '')) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><?= e(__t('consultations.height')) ?></label>
                    <input type="number" step="0.1" name="height" id="height" class="form-control" value="<?= e((string)($c['height'] ?? '')) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><?= e(__t('consultations.bmi')) ?></label>
                    <input type="text" id="bmi" class="form-control" value="<?= e((string)($c['bmi'] ?? '')) ?>" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 9: Physical Exam -->
    <div class="consultation-section">
        <div class="section-header">9. <?= e(__t('consultations.physical_exam')) ?></div>
        <div class="section-body">
            <textarea name="physical_examination" class="form-control" rows="5"><?= e($c['physical_examination'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Section 10: Assessment -->
    <div class="consultation-section">
        <div class="section-header">10. <?= e(__t('consultations.assessment')) ?></div>
        <div class="section-body">
            <textarea name="assessment" class="form-control" rows="4"><?= e($c['assessment'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Section 11: Diagnoses -->
    <div class="consultation-section">
        <div class="section-header">11. <?= e(__t('consultations.diagnoses')) ?> (CIE-10)</div>
        <div class="section-body">
            <div id="diagnoses-container">
                <?php if (!empty($diagnoses)): ?>
                    <?php foreach ($diagnoses as $i => $dx): ?>
                    <div class="diagnosis-row row g-2 mb-2 align-items-end">
                        <div class="col-md-2 position-relative">
                            <input type="text" class="form-control icd10-search" value="<?= e($dx['icd10_code'] ?? '') ?>" placeholder="CIE-10" autocomplete="off">
                            <div class="icd10-results"></div>
                            <input type="hidden" name="diagnosis_code[]" class="diagnosis-code" value="<?= e($dx['icd10_code'] ?? '') ?>">
                            <input type="hidden" name="diagnosis_icd10_id[]" class="diagnosis-icd10-id" value="<?= (int)($dx['icd10_id'] ?? 0) ?>">
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="diagnosis_description[]" class="form-control diagnosis-description" value="<?= e($dx['description']) ?>">
                        </div>
                        <div class="col-md-1">
                            <div class="form-check">
                                <input type="radio" name="diagnosis_primary" value="<?= $i ?>" class="form-check-input" <?= !empty($dx['is_primary']) ? 'checked' : '' ?>>
                                <label class="form-check-label small">P</label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-diagnosis">&times;</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="diagnosis-row row g-2 mb-2 align-items-end">
                        <div class="col-md-2 position-relative">
                            <input type="text" class="form-control icd10-search" placeholder="CIE-10" autocomplete="off">
                            <div class="icd10-results"></div>
                            <input type="hidden" name="diagnosis_code[]" class="diagnosis-code">
                            <input type="hidden" name="diagnosis_icd10_id[]" class="diagnosis-icd10-id">
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="diagnosis_description[]" class="form-control diagnosis-description" placeholder="Descripción del diagnóstico">
                        </div>
                        <div class="col-md-1">
                            <div class="form-check">
                                <input type="radio" name="diagnosis_primary" value="0" class="form-check-input" checked>
                                <label class="form-check-label small">P</label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-diagnosis">&times;</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" id="add-diagnosis" class="btn-palmed-outline btn-palmed-sm mt-2">+ <?= e(__t('consultations.add_diagnosis')) ?></button>
        </div>
    </div>

    <!-- Sections 12-16 -->
    <div class="consultation-section">
        <div class="section-header">12. <?= e(__t('consultations.management_plan')) ?></div>
        <div class="section-body"><textarea name="management_plan" class="form-control" rows="4"><?= e($c['management_plan'] ?? '') ?></textarea></div>
    </div>
    <div class="consultation-section">
        <div class="section-header">13. <?= e(__t('consultations.medications')) ?></div>
        <div class="section-body"><textarea name="medications_prescribed" class="form-control" rows="4"><?= e($c['medications_prescribed'] ?? '') ?></textarea></div>
    </div>
    <div class="consultation-section">
        <div class="section-header">14. <?= e(__t('consultations.medical_orders')) ?></div>
        <div class="section-body"><textarea name="medical_orders" class="form-control" rows="3"><?= e($c['medical_orders'] ?? '') ?></textarea></div>
    </div>
    <div class="consultation-section">
        <div class="section-header">15. <?= e(__t('consultations.recommendations')) ?></div>
        <div class="section-body"><textarea name="recommendations" class="form-control" rows="3"><?= e($c['recommendations'] ?? '') ?></textarea></div>
    </div>
    <div class="consultation-section">
        <div class="section-header">16. <?= e(__t('consultations.follow_up')) ?></div>
        <div class="section-body"><textarea name="follow_up_plan" class="form-control" rows="3"><?= e($c['follow_up_plan'] ?? '') ?></textarea></div>
    </div>

    <!-- Section 17: Medical Leave -->
    <div class="consultation-section">
        <div class="section-header">17. <?= e(__t('consultations.medical_leave')) ?></div>
        <div class="section-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label"><?= e(__t('consultations.leave_days')) ?></label>
                    <input type="number" name="medical_leave_days" class="form-control" value="<?= e((string)($c['medical_leave_days'] ?? '')) ?>" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= e(__t('consultations.leave_from')) ?></label>
                    <input type="date" name="medical_leave_from" class="form-control" value="<?= e($c['medical_leave_from'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= e(__t('consultations.leave_to')) ?></label>
                    <input type="date" name="medical_leave_to" class="form-control" value="<?= e($c['medical_leave_to'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label"><?= e(__t('consultations.leave_reason')) ?></label>
                    <textarea name="medical_leave_reason" class="form-control" rows="2"><?= e($c['medical_leave_reason'] ?? '') ?></textarea>
                </div>
    <!-- Section 18: Digital Signature -->
    <div class="consultation-section mb-5">
        <div class="section-header">18. <?= e(__t('consultations.digital_signature')) ?></div>
        <div class="section-body">
            <div class="signature-pad-wrapper">
                <canvas id="signature-canvas"></canvas>
            </div>
            <input type="hidden" name="digital_signature" id="digital_signature" value="<?= e($c['digital_signature'] ?? '') ?>">
            <button type="button" id="clear-signature" class="btn-palmed-outline btn-palmed-sm mt-2"><?= e(__t('consultations.clear_signature')) ?></button>
        </div>

<div class="consultation-toolbar">

    <button
        type="button"
        id="save-consultation"
        class="btn-palmed btn-palmed-lg">
        <?= e(__t('consultations.save')) ?>
    </button>

    <?php if ($isEdit): ?>

        <a
            href="<?= url('consultations/' . $c['id'] . '/print') ?>"
            target="_blank"
            class="btn-palmed-secondary btn-palmed-lg text-decoration-none">
            <?= e(__t('consultations.generate_pdf')) ?>
        </a>

        <button
            type="button"
            class="btn-palmed-outline btn-palmed-lg"
            onclick="window.open('<?= url('consultations/' . $c['id'] . '/print') ?>','_blank')">
            <?= e(__t('consultations.print')) ?>
        </button>

    <?php else: ?>

        <button
            type="button"
            class="btn-palmed-secondary btn-palmed-lg"
            disabled>
            <?= e(__t('consultations.generate_pdf')) ?>
        </button>

        <button
            type="button"
            class="btn-palmed-outline btn-palmed-lg"
            disabled>
            <?= e(__t('consultations.print')) ?>
        </button>

    <?php endif; ?>

    <button
        type="button"
        class="btn-palmed-outline btn-palmed-lg"
        disabled>
        <?= e(__t('consultations.send_pdf')) ?>
    </button>

</div>
</form>
<?php
$content = ob_get_clean();
require PALMED_VIEWS . '/layouts/app.php';

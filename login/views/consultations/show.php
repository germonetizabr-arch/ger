<?php
$pageTitle = __t('consultations.title');
$activeNav = 'consultations';
ob_start();
$c = $consultation;
?>
<div class="top-bar">
    <div>
        <h1 class="page-title"><?= e(__t('consultations.title')) ?></h1>
        <p class="text-muted mb-0">
            <?= e($c['patient_first'] . ' ' . $c['patient_last']) ?> —
            <?= e(format_datetime($c['consultation_date'])) ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <?php if (\Palmed\Core\Auth::can('consultations.manage')): ?>
        <a href="<?= url('consultations/' . $c['id'] . '/edit') ?>" class="btn-palmed btn-palmed-lg"><?= e(__t('edit')) ?></a>
        <?php endif; ?>
        <a href="<?= url('patients/' . $c['patient_id']) ?>" class="btn-palmed-outline btn-palmed-lg"><?= e(__t('patients.profile')) ?></a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Paciente:</strong> <?= e($c['patient_first'] . ' ' . $c['patient_last']) ?><br>
                <strong>Documento:</strong> <?= e($c['document_type'] . ' ' . $c['document_number']) ?><br>
                <strong>Edad:</strong> <?= e((string)($c['age'] ?? '')) ?>
            </div>
            <div class="col-md-6">
                <strong>Médico:</strong> Dr. <?= e($c['physician_first'] . ' ' . $c['physician_last']) ?><br>
                <strong>Licencia:</strong> <?= e($c['professional_license'] ?? '-') ?><br>
                <strong>Especialidad:</strong> <?= e($c['specialty_name'] ?? '-') ?>
            </div>
        </div>
    </div>
</div>

<?php
$sections = [
    'reason_for_consultation' => __t('consultations.reason'),
    'current_illness' => __t('consultations.current_illness'),
    'past_medical_history' => __t('consultations.past_history'),
    'surgical_history' => __t('consultations.surgical_history'),
    'family_history' => __t('consultations.family_history'),
    'allergies' => __t('consultations.allergies'),
    'current_medications' => __t('consultations.current_medications'),
    'physical_examination' => __t('consultations.physical_exam'),
    'assessment' => __t('consultations.assessment'),
    'management_plan' => __t('consultations.management_plan'),
    'medications_prescribed' => __t('consultations.medications'),
    'medical_orders' => __t('consultations.medical_orders'),
    'recommendations' => __t('consultations.recommendations'),
    'follow_up_plan' => __t('consultations.follow_up'),
];
?>

<div class="card mb-4">
    <div class="card-header"><?= e(__t('consultations.vital_signs')) ?></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-auto"><strong>PA:</strong> <?= e((string)($c['blood_pressure_systolic'] ?? '-')) ?>/<?= e((string)($c['blood_pressure_diastolic'] ?? '-')) ?> mmHg</div>
            <div class="col-auto"><strong>FC:</strong> <?= e((string)($c['heart_rate'] ?? '-')) ?> lpm</div>
            <div class="col-auto"><strong>FR:</strong> <?= e((string)($c['respiratory_rate'] ?? '-')) ?> rpm</div>
            <div class="col-auto"><strong>Temp:</strong> <?= e((string)($c['temperature'] ?? '-')) ?> °C</div>
            <div class="col-auto"><strong>Peso:</strong> <?= e((string)($c['weight'] ?? '-')) ?> kg</div>
            <div class="col-auto"><strong>Talla:</strong> <?= e((string)($c['height'] ?? '-')) ?> cm</div>
            <div class="col-auto"><strong>IMC:</strong> <?= e((string)($c['bmi'] ?? '-')) ?></div>
        </div>
    </div>
</div>

<?php foreach ($sections as $field => $label): ?>
    <?php if (!empty($c[$field])): ?>
    <div class="card mb-3">
        <div class="card-header"><?= e($label) ?></div>
        <div class="card-body"><?= nl2br(e($c[$field])) ?></div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($diagnoses)): ?>
<div class="card mb-3">
    <div class="card-header"><?= e(__t('consultations.diagnoses')) ?></div>
    <div class="card-body">
        <ul class="mb-0">
            <?php foreach ($diagnoses as $dx): ?>
            <li>
                <?php if ($dx['icd10_code']): ?><strong><?= e($dx['icd10_code']) ?></strong> — <?php endif; ?>
                <?= e($dx['description']) ?>
                <?php if ($dx['is_primary']): ?><span class="badge bg-primary ms-1"><?= e(__t('consultations.primary_diagnosis')) ?></span><?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($c['digital_signature'])): ?>
<div class="card mb-3">
    <div class="card-header"><?= e(__t('consultations.digital_signature')) ?></div>
    <div class="card-body">
        <img src="<?= e($c['digital_signature']) ?>" alt="Firma" style="max-height:100px;">
        <?php if ($c['signed_at']): ?>
        <p class="text-muted small mt-2 mb-0">Firmado: <?= e(format_datetime($c['signed_at'])) ?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require PALMED_VIEWS . '/layouts/app.php';

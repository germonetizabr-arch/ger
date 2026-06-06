<?php
$pageTitle = __t('patients.profile');
$activeNav = 'patients';
ob_start();
?>
<div class="top-bar">
    <div>
        <h1 class="page-title"><?= e($patient['first_name'] . ' ' . $patient['last_name']) ?></h1>
        <p class="text-muted mb-0"><?= e($patient['document_type'] . ' ' . $patient['document_number']) ?></p>
    </div>
    <div class="d-flex gap-2">
        <?php if (\Palmed\Core\Auth::can('consultations.manage')): ?>
        <a href="<?= url('patients/' . $patient['id'] . '/consultation') ?>" class="btn-palmed-secondary btn-palmed-lg"><?= e(__t('patients.new_consultation')) ?></a>
        <?php endif; ?>
        <?php if (\Palmed\Core\Auth::can('patients.manage')): ?>
        <a href="<?= url('patients/' . $patient['id'] . '/edit') ?>" class="btn-palmed-outline btn-palmed-lg"><?= e(__t('edit')) ?></a>
        <?php endif; ?>
        <a href="<?= url('patients') ?>" class="btn-palmed-outline btn-palmed-lg"><?= e(__t('back')) ?></a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><?= e(__t('patients.demographics')) ?></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted"><?= e(__t('patients.date_of_birth')) ?></dt>
                    <dd class="col-7"><?= e(format_date($patient['date_of_birth'])) ?></dd>
                    <dt class="col-5 text-muted"><?= e(__t('patients.age')) ?></dt>
                    <dd class="col-7"><?= e((string) ($patient['age'] ?? '')) ?></dd>
                    <dt class="col-5 text-muted"><?= e(__t('patients.sex')) ?></dt>
                    <dd class="col-7"><?= e(__t('patients.sex_' . ($patient['sex'] === 'M' ? 'male' : ($patient['sex'] === 'F' ? 'female' : 'other')))) ?></dd>
                    <dt class="col-5 text-muted"><?= e(__t('patients.phone')) ?></dt>
                    <dd class="col-7"><?= e($patient['phone']) ?></dd>
                    <dt class="col-5 text-muted"><?= e(__t('patients.email')) ?></dt>
                    <dd class="col-7"><?= e($patient['email']) ?></dd>
                    <dt class="col-5 text-muted"><?= e(__t('patients.address')) ?></dt>
                    <dd class="col-7"><?= e($patient['address']) ?></dd>
                    <dt class="col-5 text-muted"><?= e(__t('patients.occupation')) ?></dt>
                    <dd class="col-7"><?= e($patient['occupation']) ?></dd>
                    <dt class="col-5 text-muted"><?= e(__t('patients.emergency_contact')) ?></dt>
                    <dd class="col-7"><?= e($patient['emergency_contact_name']) ?> — <?= e($patient['emergency_contact_phone']) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><?= e(__t('patients.consultation_history')) ?></div>
            <div class="card-body p-0">
                <?php if (empty($consultations)): ?>
                    <p class="text-muted p-4 mb-0"><?= e(__t('patients.no_consultations')) ?></p>
                <?php else: ?>
                    <table class="table-palmed mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Médico</th>
                                <th>Especialidad</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

<?php foreach ($consultations as $c): ?>

<tr>

    <td><?= e(format_datetime($c['consultation_date'])) ?></td>

    <td>
        Dr. <?= e($c['physician_first'] . ' ' . $c['physician_last']) ?>
    </td>

    <td>
        <?= e($c['specialty_name'] ?? '-') ?>
    </td>

    <td>
        <span class="badge-palmed badge-<?= e($c['status']) ?>">
            <?= e(__t('consultations.status_' . $c['status'])) ?>
        </span>
    </td>

    <td style="white-space:nowrap;">

        <a href="<?= url('consultations/' . $c['id']) ?>"
           class="btn-palmed-outline btn-palmed-sm">
            👁️ Ver
        </a>

        <a href="<?= url('consultations/' . $c['id'] . '/print') ?>"
           target="_blank"
           class="btn-palmed btn-palmed-sm">
            🖨️ Imprimir
        </a>

    </td>

</tr>

<?php endforeach; ?>

</tbody>
</table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require PALMED_VIEWS . '/layouts/app.php';

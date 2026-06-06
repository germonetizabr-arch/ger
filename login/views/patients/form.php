<?php
$isEdit = $action === 'edit';
$pageTitle = $isEdit ? __t('patients.edit') : __t('patients.new');
$activeNav = 'patients';
$p = $patient ?? [];
ob_start();
?>
<div class="top-bar">
    <h1 class="page-title"><?= e($pageTitle) ?></h1>
    <a href="<?= url('patients') ?>" class="btn-palmed-outline"><?= e(__t('back')) ?></a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? url('patients/' . $p['id']) : url('patients') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><?= e(__t('patients.first_name')) ?> *</label>
                    <input type="text" name="first_name" class="form-control" value="<?= e(old('first_name', $p['first_name'] ?? '')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(__t('patients.last_name')) ?> *</label>
                    <input type="text" name="last_name" class="form-control" value="<?= e(old('last_name', $p['last_name'] ?? '')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= e(__t('patients.document_type')) ?></label>
                    <select name="document_type" class="form-select">
                        <?php foreach (['CC' => 'Cédula', 'TI' => 'Tarjeta de identidad', 'CE' => 'Cédula extranjería', 'PA' => 'Pasaporte', 'RC' => 'Registro civil'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= (old('document_type', $p['document_type'] ?? 'CC') === $val) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= e(__t('patients.document_number')) ?> *</label>
                    <input type="text" name="document_number" class="form-control" value="<?= e(old('document_number', $p['document_number'] ?? '')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= e(__t('patients.date_of_birth')) ?></label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="<?= e(old('date_of_birth', $p['date_of_birth'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= e(__t('patients.age')) ?></label>
                    <input type="number" name="age" id="age" class="form-control" value="<?= e(old('age', $p['age'] ?? '')) ?>" min="0" max="150">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= e(__t('patients.sex')) ?></label>
                    <select name="sex" class="form-select">
                        <option value="M" <?= (old('sex', $p['sex'] ?? '') === 'M') ? 'selected' : '' ?>><?= e(__t('patients.sex_male')) ?></option>
                        <option value="F" <?= (old('sex', $p['sex'] ?? '') === 'F') ? 'selected' : '' ?>><?= e(__t('patients.sex_female')) ?></option>
                        <option value="O" <?= (old('sex', $p['sex'] ?? 'O') === 'O') ? 'selected' : '' ?>><?= e(__t('patients.sex_other')) ?></option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(__t('patients.phone')) ?></label>
                    <input type="tel" name="phone" class="form-control" value="<?= e(old('phone', $p['phone'] ?? '')) ?>">
                </div>
                <div class="col-md-5">
                    <label class="form-label"><?= e(__t('patients.email')) ?></label>
                    <input type="email" name="email" class="form-control" value="<?= e(old('email', $p['email'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(__t('patients.address')) ?></label>
                    <input type="text" name="address" class="form-control" value="<?= e(old('address', $p['address'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(__t('patients.occupation')) ?></label>
                    <input type="text" name="occupation" class="form-control" value="<?= e(old('occupation', $p['occupation'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(__t('patients.emergency_contact')) ?></label>
                    <input type="text" name="emergency_contact_name" class="form-control" value="<?= e(old('emergency_contact_name', $p['emergency_contact_name'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(__t('patients.emergency_phone')) ?></label>
                    <input type="tel" name="emergency_contact_phone" class="form-control" value="<?= e(old('emergency_contact_phone', $p['emergency_contact_phone'] ?? '')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea name="notes" class="form-control" rows="3"><?= e(old('notes', $p['notes'] ?? '')) ?></textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-palmed btn-palmed-lg"><?= e(__t('save')) ?></button>
                <a href="<?= url('patients') ?>" class="btn-palmed-outline btn-palmed-lg"><?= e(__t('cancel')) ?></a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require PALMED_VIEWS . '/layouts/app.php';

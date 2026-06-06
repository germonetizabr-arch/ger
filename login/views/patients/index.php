<?php
$pageTitle = __t('patients.title');
$activeNav = 'patients';
ob_start();
$totalPages = (int) ceil($total / $perPage);
?>
<div class="top-bar">
    <h1 class="page-title"><?= e(__t('patients.title')) ?></h1>
    <?php if (\Palmed\Core\Auth::can('patients.manage')): ?>
    <a href="<?= url('patients/create') ?>" class="btn-palmed btn-palmed-lg">+ <?= e(__t('patients.new')) ?></a>
    <?php endif; ?>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('patients') ?>" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="q" class="form-control" placeholder="<?= e(__t('patients.search_placeholder')) ?>" value="<?= e($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-palmed w-100 justify-content-center"><?= e(__t('search')) ?></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($patients)): ?>
            <p class="text-muted p-4 mb-0"><?= e(__t('no_results')) ?></p>
        <?php else: ?>
            <table class="table-palmed mb-0">
                <thead>
                    <tr>
                        <th><?= e(__t('patients.first_name')) ?></th>
                        <th><?= e(__t('patients.document_number')) ?></th>
                        <th><?= e(__t('patients.phone')) ?></th>
                        <th><?= e(__t('patients.email')) ?></th>
                        <th><?= e(__t('actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $p): ?>
                    <tr>
                        <td><?= e($p['first_name'] . ' ' . $p['last_name']) ?></td>
                        <td><?= e($p['document_type'] . ' ' . $p['document_number']) ?></td>
                        <td><?= e($p['phone']) ?></td>
                        <td><?= e($p['email']) ?></td>
                        <td>
                            <a href="<?= url('patients/' . $p['id']) ?>" class="btn-palmed-outline btn-palmed-sm"><?= e(__t('view')) ?></a>
                            <?php if (\Palmed\Core\Auth::can('patients.manage')): ?>
                            <a href="<?= url('patients/' . $p['id'] . '/edit') ?>" class="btn-palmed-outline btn-palmed-sm"><?= e(__t('edit')) ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- Hidden items for patient search autocomplete -->
                    <tr class="d-none patient-search-item" data-id="<?= (int) $p['id'] ?>" data-name="<?= e($p['first_name'] . ' ' . $p['last_name'] . ' - ' . $p['document_number']) ?>">
                        <td colspan="5"><?= e($p['first_name'] . ' ' . $p['last_name']) ?> (<?= e($p['document_number']) ?>)</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php if ($totalPages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= url('patients?page=' . $i . ($search ? '&q=' . urlencode($search) : '')) ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
<?php
$content = ob_get_clean();
require PALMED_VIEWS . '/layouts/app.php';

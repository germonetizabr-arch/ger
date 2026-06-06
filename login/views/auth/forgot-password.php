<?php
$pageTitle = __t('auth.reset_password');
ob_start();
?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <h1><?= e(__t('app_name')) ?></h1>
        </div>
        <h4 class="text-center mb-4"><?= e(__t('auth.reset_password')) ?></h4>

        <?php if ($msg = flash('error')): ?>
            <div class="alert-palmed alert-error"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('forgot-password') ?>">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="form-label"><?= e(__t('auth.email')) ?></label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <button type="submit" class="btn-palmed btn-palmed-lg w-100 justify-content-center mb-3">
                <?= e(__t('auth.reset_password')) ?>
            </button>
            <a href="<?= url('login') ?>" class="btn-palmed-outline btn-palmed w-100 justify-content-center">
                <?= e(__t('back')) ?>
            </a>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require PALMED_VIEWS . '/layouts/auth.php';

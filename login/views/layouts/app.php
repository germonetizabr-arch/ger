<!DOCTYPE html>
<html lang="<?= e($_SESSION['language'] ?? config('default_language', 'es')) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'PALMED Clinic') ?> - PALMED Clinic</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= asset('css/palmed.css') ?>" rel="stylesheet">

    <script>
        window.PALMED_BASE_URL = '<?= e(url()) ?>';
    </script>
</head>

<body>

<div class="app-layout">

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand text-center py-4">

            <img
                src="<?= asset('img/palmed-logo2.png') ?>"
                alt="PALMED Clinic"
                style="
                    width:90px;
                    height:auto;
                    display:block;
                    margin:0 auto 12px auto;
                "
            >

            <h5 style="
                color:#ffffff;
                font-weight:700;
                margin-bottom:2px;
            ">
                PALMED Clinic
            </h5>

            <small style="
                display:block;
                color:rgba(255,255,255,.85);
                font-size:12px;
            ">
                by PALMED Health Group S.A.S.
            </small>

            <small style="
                display:block;
                color:rgba(255,255,255,.70);
                font-size:11px;
                margin-top:4px;
            ">
                NIT 902070469-1
            </small>

        </div>

        <nav class="sidebar-nav">

            <a href="<?= url('dashboard') ?>" class="<?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
                <span>📊</span> <?= e(__t('nav.dashboard')) ?>
            </a>

            <?php if (\Palmed\Core\Auth::can('patients.view')): ?>
            <a href="<?= url('patients') ?>" class="<?= ($activeNav ?? '') === 'patients' ? 'active' : '' ?>">
                <span>👥</span> <?= e(__t('nav.patients')) ?>
            </a>
            <?php endif; ?>

            <?php if (\Palmed\Core\Auth::can('consultations.manage')): ?>
            <a href="<?= url('consultations/create') ?>" class="<?= ($activeNav ?? '') === 'consultations' ? 'active' : '' ?>">
                <span>🩺</span> <?= e(__t('nav.consultations')) ?>
            </a>
            <?php endif; ?>

            <?php if (($_SESSION['role_slug'] ?? '') === 'super_admin'): ?>
            <a href="<?= url('users') ?>" class="<?= ($activeNav ?? '') === 'users' ? 'active' : '' ?>">
                <span>⚙️</span> Usuarios
            </a>
            <?php endif; ?>

        </nav>

        <div class="px-4 mt-4 pt-4 border-top">

            <div class="small text-light mb-2">
                <?= e($_SESSION['user_name'] ?? '') ?>
            </div>

            <a href="<?= url('logout') ?>"
               class="btn-palmed-outline btn-palmed-sm w-100 justify-content-center">
                <?= e(__t('auth.logout')) ?>
            </a>

        </div>

    </aside>

    <main class="main-content">

        <?php if ($msg = flash('success')): ?>
            <div class="alert-palmed alert-success" data-auto-dismiss>
                <?= e($msg) ?>
            </div>
        <?php endif; ?>

        <?php if ($msg = flash('error')): ?>
            <div class="alert-palmed alert-error" data-auto-dismiss>
                <?= e($msg) ?>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/palmed.js') ?>"></script>

<?php if (!empty($extraScripts)): ?>
    <?php foreach ($extraScripts as $script): ?>
        <script src="<?= asset('js/' . $script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>

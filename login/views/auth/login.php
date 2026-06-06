<?php
$pageTitle = 'PALMED Clinic';
ob_start();
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="text-center mb-4">

            <img
                src="/assets/img/palmed-logo.png?v=<?= time() ?>"
                alt="PALMED Clinic"
                style="
                    width:140px;
                    height:auto;
                    display:block;
                    margin:0 auto;
                "
            >

        </div>

        <div class="text-center mb-4">

            <h2 style="
                font-weight:700;
                color:#0b2f6b;
                margin-bottom:5px;
            ">
                PALMED Clinic
            </h2>

            <div style="
                color:#16a085;
                font-weight:600;
            ">
                by PALMED Health Group S.A.S.
            </div>

            <div style="
                font-size:13px;
                color:#6c757d;
            ">
                NIT 902070469-1
            </div>

        </div>

        <h3 class="text-center mb-2">
            Iniciar sesión
        </h3>

        <p class="text-center text-muted mb-4">
            Sistema de Historia Clínica Electrónica
        </p>

        <?php if ($msg = flash('error')): ?>
            <div class="alert-palmed alert-error">
                <?= e($msg) ?>
            </div>
        <?php endif; ?>

        <?php if ($msg = flash('success')): ?>
            <div class="alert-palmed alert-success">
                <?= e($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('login') ?>">

            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">
                    Usuario
                </label>

                <input
                    type="text"
                    name="email"
                    class="form-control"
                    value="<?= e(old('email')) ?>"
                    required
                    autofocus
                >
            </div>

            <div class="mb-3">

                <label class="form-label">
                    Contraseña
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn-palmed btn-palmed-lg w-100 justify-content-center"
            >
                Ingresar
            </button>

        </form>

        <div class="text-center mt-4">

            <small style="color:#6c757d;">
                © <?= date('Y') ?> PALMED Health Group S.A.S.
            </small>

            <br>

            <small style="color:#6c757d;">
                NIT 902070469-1
            </small>

        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require PALMED_VIEWS . '/layouts/auth.php';

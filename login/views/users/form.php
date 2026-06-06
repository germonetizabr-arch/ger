<?php

$isEdit = isset($user);

$pageTitle = $isEdit ? 'Editar Usuario' : 'Nuevo Usuario';

ob_start();
?>

<div class="container-fluid">

    <h2 class="mb-4">
        <?= $isEdit ? 'Editar Usuario' : 'Nuevo Usuario' ?>
    </h2>

    <div class="card">

        <div class="card-body">

            <form method="POST"
                  enctype="multipart/form-data"
                  action="<?= $isEdit
                    ? url('users/' . $user['id'])
                    : url('users') ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Nombres</label>

                        <input
                            type="text"
                            name="first_name"
                            class="form-control"
                            value="<?= e($user['first_name'] ?? '') ?>"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Apellidos</label>

                        <input
                            type="text"
                            name="last_name"
                            class="form-control"
                            value="<?= e($user['last_name'] ?? '') ?>"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Usuario</label>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            value="<?= e($user['username'] ?? '') ?>"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Correo</label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= e($user['email'] ?? '') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Contraseña</label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            <?= $isEdit ? '' : 'required' ?>>

                        <?php if ($isEdit): ?>
                            <small class="text-muted">
                                Déjelo vacío para conservar la contraseña actual.
                            </small>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Rol</label>

                        <select
                            name="role_id"
                            class="form-control"
                            required>

                            <?php foreach ($roles as $role): ?>

                                <option
                                    value="<?= $role['id'] ?>"
                                    <?= (($user['role_id'] ?? 0) == $role['id']) ? 'selected' : '' ?>>

                                    <?= e($role['name']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <?php if ($isEdit): ?>

                    <div class="col-md-6 mb-3">

                        <label>Estado</label>

                        <select
                            name="is_active"
                            class="form-control">

                            <option value="1"
                                <?= (($user['is_active'] ?? 1) == 1) ? 'selected' : '' ?>>
                                Activo
                            </option>

                            <option value="0"
                                <?= (($user['is_active'] ?? 1) == 0) ? 'selected' : '' ?>>
                                Inactivo
                            </option>

                        </select>

                    </div>

                    <?php endif; ?>

                    <?php if ($isEdit): ?>

                    <div class="col-12 mt-3">

                        <hr>

                        <h5>Firma Electrónica</h5>

                    </div>

                    <div class="col-md-8 mb-3">

                        <label>Archivo de Firma (PNG/JPG)</label>

                        <input
                            type="file"
                            name="signature"
                            class="form-control"
                            accept=".png,.jpg,.jpeg">

                    </div>

                    <?php if (!empty($user['signature_path'])): ?>

                    <div class="col-12 mb-3">

                        <label>Firma actual</label>

                        <div>

                            <img
                                src="<?= url($user['signature_path']) ?>"
                                style="
                                    max-height:120px;
                                    background:#fff;
                                    border:1px solid #ddd;
                                    padding:10px;
                                ">

                        </div>

                    </div>

                    <?php endif; ?>

                    <?php endif; ?>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <?= $isEdit ? 'Actualizar Usuario' : 'Guardar Usuario' ?>

                </button>

                <a href="<?= url('users') ?>"
                   class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

<?php

$content = ob_get_clean();

require PALMED_VIEWS . '/layouts/app.php';
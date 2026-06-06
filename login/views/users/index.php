<?php

$pageTitle = 'Usuarios';

ob_start();
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Usuarios</h2>

        <a href="<?= url('users/create') ?>"
           class="btn btn-primary">
            Nuevo Usuario
        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <table class="table table-striped">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($users as $user): ?>

                    <tr>

                        <td>
                            <?= $user['id'] ?>
                        </td>

                        <td>
                            <?= e($user['username'] ?? '') ?>
                        </td>

                        <td>
                            <?= e($user['first_name'] . ' ' . $user['last_name']) ?>
                        </td>

                        <td>
                            <?= e($user['email']) ?>
                        </td>

                        <td>
                            <?= e($user['role_name']) ?>
                        </td>

                        <td>
                            <?= $user['is_active'] ? 'Activo' : 'Inactivo' ?>
                        </td>

                        <td>

                            <a href="<?= url('users/' . $user['id'] . '/edit') ?>"
                               class="btn btn-sm btn-primary">
                                Editar
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php

$content = ob_get_clean();

require PALMED_VIEWS . '/layouts/app.php';
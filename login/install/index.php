<?php
/**
 * PALMED Clinic - Installation Wizard
 */

declare(strict_types=1);

define('PALMED_ROOT', dirname(__DIR__));
define('PALMED_CONFIG', PALMED_ROOT . '/config');
define('INSTALL_LOCK', PALMED_ROOT . '/install/install.lock');

session_start();

function runSqlFile(PDO $pdo, string $filepath): void
{
    $sql = file_get_contents($filepath);
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }
}

if (file_exists(INSTALL_LOCK)) {
    die('<h2>PALMED Clinic ya está instalado.</h2><p><a href="../">Ir al sistema</a></p>');
}

$step = max(1, min(4, (int) ($_GET['step'] ?? $_POST['step'] ?? 1)));
$errors = [];
$success = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1:
            $dbHost = trim($_POST['db_host'] ?? 'localhost');
            $dbPort = (int) ($_POST['db_port'] ?? 3306);
            $dbName = trim($_POST['db_name'] ?? '');
            $dbUser = trim($_POST['db_user'] ?? '');
            $dbPass = $_POST['db_password'] ?? '';

            if ($dbName === '' || $dbUser === '') {
                $errors[] = 'Nombre de base de datos y usuario son requeridos.';
                break;
            }

            try {
                $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
                $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `{$dbName}`");

                runSqlFile($pdo, PALMED_ROOT . '/database/schema.sql');

                $_SESSION['install_db'] = compact('dbHost', 'dbPort', 'dbName', 'dbUser', 'dbPass');
                header('Location: ?step=2');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Error de conexión: ' . $e->getMessage();
            }
            break;

        case 2:
            if (empty($_SESSION['install_db'])) {
                header('Location: ?step=1');
                exit;
            }

            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['password_confirm'] ?? '';

            if ($firstName === '' || $lastName === '' || $email === '') {
                $errors[] = 'Todos los campos son requeridos.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Correo electrónico inválido.';
            } elseif (strlen($password) < 8) {
                $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif ($password !== $confirm) {
                $errors[] = 'Las contraseñas no coinciden.';
            } else {
                $_SESSION['install_admin'] = compact('firstName', 'lastName', 'email', 'password');
                header('Location: ?step=3');
                exit;
            }
            break;

        case 3:
            if (empty($_SESSION['install_db']) || empty($_SESSION['install_admin'])) {
                header('Location: ?step=1');
                exit;
            }

            $appUrl = rtrim(trim($_POST['app_url'] ?? ''), '/');
            $companyName = trim($_POST['company_name'] ?? 'PALMED Health Group S.A.S.');
            $timezone = trim($_POST['timezone'] ?? 'America/Bogota');
            $language = $_POST['language'] ?? 'es';

            $_SESSION['install_settings'] = compact('appUrl', 'companyName', 'timezone', 'language');
            header('Location: ?step=4');
            exit;

        case 4:
            if (empty($_SESSION['install_db']) || empty($_SESSION['install_admin']) || empty($_SESSION['install_settings'])) {
                header('Location: ?step=1');
                exit;
            }

            try {
                $db = $_SESSION['install_db'];
                $admin = $_SESSION['install_admin'];
                $settings = $_SESSION['install_settings'];

                $dsn = "mysql:host={$db['dbHost']};port={$db['dbPort']};dbname={$db['dbName']};charset=utf8mb4";
                $pdo = new PDO($dsn, $db['dbUser'], $db['dbPass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

                // Insert roles, permissions, sample data
                runSqlFile($pdo, PALMED_ROOT . '/database/sample_data.sql');

                // Create super admin user
                $hash = password_hash($admin['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    'INSERT INTO users (role_id, email, password, first_name, last_name, language, is_active)
                     VALUES (1, ?, ?, ?, ?, ?, 1)'
                );
                $stmt->execute([$admin['email'], $hash, $admin['firstName'], $admin['lastName'], $settings['language']]);

                // Insert settings
                $settingsData = [
                    'company_name' => $settings['companyName'],
                    'default_language' => $settings['language'],
                    'timezone' => $settings['timezone'],
                    'installed_at' => date('Y-m-d H:i:s'),
                    'app_version' => '1.0.0',
                ];
                $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)');
                foreach ($settingsData as $key => $value) {
                    $stmt->execute([$key, $value]);
                }

                // Write config file
                $configContent = "<?php\nreturn " . var_export([
                    'app_name' => 'PALMED Clinic',
                    'company_name' => $settings['companyName'],
                    'app_url' => $settings['appUrl'],
                    'app_version' => '1.0.0',
                    'timezone' => $settings['timezone'],
                    'default_language' => $settings['language'],
                    'installed' => true,
                    'database' => [
                        'host' => $db['dbHost'],
                        'port' => $db['dbPort'],
                        'name' => $db['dbName'],
                        'user' => $db['dbUser'],
                        'password' => $db['dbPass'],
                        'charset' => 'utf8mb4',
                    ],
                    'session' => [
                        'name' => 'PALMED_SESSION',
                        'lifetime' => 7200,
                        'remember_days' => 30,
                    ],
                    'upload' => [
                        'max_size' => 10485760,
                        'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx'],
                    ],
                    'colors' => [
                        'primary' => '#0A5FD8',
                        'secondary' => '#00A86B',
                        'white' => '#FFFFFF',
                    ],
                ], true) . ";\n";

                file_put_contents(PALMED_CONFIG . '/config.php', $configContent);
                file_put_contents(INSTALL_LOCK, date('Y-m-d H:i:s'));

                // Secure uploads directory
                @file_put_contents(PALMED_ROOT . '/uploads/.htaccess', "Options -Indexes\n<FilesMatch \"\\.(php|phtml|php3|php4|php5)$\">\nDeny from all\n</FilesMatch>\n");

                session_destroy();
                $success = 'install_complete';
            } catch (Exception $e) {
                $errors[] = 'Error durante la instalación: ' . $e->getMessage();
            }
            break;
    }
}

$appUrlDefault = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
    . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - PALMED Clinic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/palmed.css" rel="stylesheet">
    <style>
        .install-wrapper { min-height: 100vh; background: linear-gradient(135deg, #0A5FD8, #00A86B); padding: 2rem; }
        .install-card { background: white; border-radius: 16px; max-width: 640px; margin: 0 auto; padding: 2.5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .step-indicator { display: flex; gap: 0.5rem; margin-bottom: 2rem; }
        .step-dot { flex: 1; height: 4px; border-radius: 2px; background: #e2e8f0; }
        .step-dot.active { background: #0A5FD8; }
        .step-dot.done { background: #00A86B; }
    </style>
</head>
<body>
<div class="install-wrapper">
    <div class="install-card">
        <div class="text-center mb-4">
            <h2 style="color:#0A5FD8;font-weight:700;">PALMED Clinic</h2>
            <p class="text-muted">Asistente de instalación — Paso <?= $step ?> de 4</p>
        </div>

        <div class="step-indicator">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="step-dot <?= $i < $step ? 'done' : ($i === $step ? 'active' : '') ?>"></div>
            <?php endfor; ?>
        </div>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>

        <?php if ($success === 'install_complete'): ?>
            <div class="text-center">
                <div style="font-size:4rem;">✅</div>
                <h4 class="mt-3">¡Instalación completada!</h4>
                <p class="text-muted">PALMED Clinic está listo para usar.</p>
                <a href="../" class="btn-palmed btn-palmed-lg mt-3">Iniciar sesión</a>
                <p class="small text-danger mt-4">Por seguridad, elimine o renombre la carpeta <code>install/</code>.</p>
            </div>
        <?php elseif ($step === 1): ?>
            <h5 class="mb-3">Paso 1: Configuración de base de datos</h5>
            <form method="POST">
                <input type="hidden" name="step" value="1">
                <div class="mb-3">
                    <label class="form-label">Servidor</label>
                    <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-4">
                        <label class="form-label">Puerto</label>
                        <input type="number" name="db_port" class="form-control" value="<?= htmlspecialchars($_POST['db_port'] ?? '3306') ?>">
                    </div>
                    <div class="col-8">
                        <label class="form-label">Nombre de BD</label>
                        <input type="text" name="db_name" class="form-control" value="<?= htmlspecialchars($_POST['db_name'] ?? 'palmed_clinic') ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="db_password" class="form-control">
                </div>
                <button type="submit" class="btn-palmed btn-palmed-lg w-100">Continuar</button>
            </form>

        <?php elseif ($step === 2): ?>
            <h5 class="mb-3">Paso 2: Crear administrador</h5>
            <form method="POST">
                <input type="hidden" name="step" value="2">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Nombres</label>
                        <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" minlength="8" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <button type="submit" class="btn-palmed btn-palmed-lg w-100">Continuar</button>
            </form>

        <?php elseif ($step === 3): ?>
            <h5 class="mb-3">Paso 3: Configuración del sistema</h5>
            <form method="POST">
                <input type="hidden" name="step" value="3">
                <div class="mb-3">
                    <label class="form-label">URL de la aplicación</label>
                    <input type="url" name="app_url" class="form-control" value="<?= htmlspecialchars($_POST['app_url'] ?? $appUrlDefault) ?>" required>
                    <small class="text-muted">Sin barra final. Ej: https://tudominio.com/palmed</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre de la empresa</label>
                    <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($_POST['company_name'] ?? 'PALMED Health Group S.A.S.') ?>">
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label">Zona horaria</label>
                        <select name="timezone" class="form-select">
                            <option value="America/Bogota">America/Bogota</option>
                            <option value="America/Mexico_City">America/Mexico_City</option>
                            <option value="America/Lima">America/Lima</option>
                            <option value="America/Santiago">America/Santiago</option>
                            <option value="UTC">UTC</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Idioma predeterminado</label>
                        <select name="language" class="form-select">
                            <option value="es">Español</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-palmed btn-palmed-lg w-100">Continuar</button>
            </form>

        <?php elseif ($step === 4): ?>
            <h5 class="mb-3">Paso 4: Finalizar instalación</h5>
            <p class="text-muted mb-4">Se crearán los roles, permisos, especialidades, códigos CIE-10 de muestra y su cuenta de administrador.</p>
            <ul class="mb-4">
                <li>4 roles del sistema</li>
                <li>14 permisos configurables</li>
                <li>6 especialidades médicas</li>
                <li>10 códigos CIE-10 de muestra</li>
                <li>5 pacientes de muestra</li>
            </ul>
            <form method="POST">
                <input type="hidden" name="step" value="4">
                <button type="submit" class="btn-palmed btn-palmed-lg w-100">Instalar PALMED Clinic</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

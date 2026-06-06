<!DOCTYPE html>
<html lang="<?= e(config('default_language', 'es')) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? __t('auth.login')) ?> - <?= e(__t('app_name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= asset('css/palmed.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $content ?? '' ?>
    <script src="<?= asset('js/palmed.js') ?>"></script>
</body>
</html>

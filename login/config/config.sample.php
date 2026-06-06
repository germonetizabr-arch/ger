<?php
/**
 * PALMED Clinic - Configuration Template
 * Copy to config.php during installation
 */

return [
    'app_name' => 'PALMED Clinic',
    'company_name' => 'PALMED Health Group S.A.S.',
    'app_url' => '',
    'app_version' => '1.0.0',
    'timezone' => 'America/Bogota',
    'default_language' => 'es',
    'installed' => false,

    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'palmed_clinic',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],

    'session' => [
        'name' => 'PALMED_SESSION',
        'lifetime' => 7200,
        'remember_days' => 30,
    ],

    'upload' => [
        'max_size' => 10485760, // 10MB
        'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx'],
    ],

    'colors' => [
        'primary' => '#0A5FD8',
        'secondary' => '#00A86B',
        'white' => '#FFFFFF',
    ],
];

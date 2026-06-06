<?php
/**
 * PALMED Clinic - Global Helper Functions
 */

declare(strict_types=1);

function config(string $key, mixed $default = null): mixed
{
    global $config;
    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (!is_array($value) || !array_key_exists($k, $value)) {
            return $default;
        }
        $value = $value[$k];
    }

    return $value;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim(config('app_url', ''), '/');
    $path = ltrim($path, '/');
    return $base . ($path ? '/' . $path : '');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['old_input'][$key] ?? $default;
}

function flash(string $key): ?string
{
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

function set_flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function set_old_input(array $data): void
{
    $_SESSION['old_input'] = $data;
}

function clear_old_input(): void
{
    unset($_SESSION['old_input']);
}

function __t(string $key, array $replace = []): string
{
    static $translations = null;

    if ($translations === null) {
        $lang = $_SESSION['language'] ?? config('default_language', 'es');
        $langFile = PALMED_ROOT . '/lang/' . $lang . '.php';
        $translations = file_exists($langFile) ? require $langFile : [];
    }

    $text = $translations[$key] ?? $key;

    foreach ($replace as $search => $value) {
        $text = str_replace(':' . $search, (string) $value, $text);
    }

    return $text;
}

function calculate_age(?string $dateOfBirth): ?int
{
    if (!$dateOfBirth) {
        return null;
    }
    $dob = new DateTime($dateOfBirth);
    $now = new DateTime();
    return (int) $dob->diff($now)->y;
}

function calculate_bmi(?float $weight, ?float $height): ?float
{
    if (!$weight || !$height || $height <= 0) {
        return null;
    }
    $heightM = $height / 100;
    return round($weight / ($heightM * $heightM), 2);
}

function format_date(?string $date, string $format = 'd/m/Y'): string
{
    if (!$date) {
        return '';
    }
    return (new DateTime($date))->format($format);
}

function format_datetime(?string $datetime, string $format = 'd/m/Y H:i'): string
{
    if (!$datetime) {
        return '';
    }
    return (new DateTime($datetime))->format($format);
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function view(string $name, array $data = []): void
{
    extract($data);
    $viewFile = PALMED_VIEWS . '/' . str_replace('.', '/', $name) . '.php';
    if (!file_exists($viewFile)) {
        throw new RuntimeException("View not found: {$name}");
    }
    require $viewFile;
}

function partial(string $name, array $data = []): void
{
    extract($data);
    require PALMED_VIEWS . '/partials/' . $name . '.php';
}

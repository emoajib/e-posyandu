<?php
// Security configuration: CSRF, session, sanitization
$is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => $host,
    'secure' => $is_https,
    'httponly' => true,
    'samesite' => 'Strict',
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken(string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput(string $input): string {
    return htmlspecialchars(trim(stripslashes($input)), ENT_QUOTES, 'UTF-8');
}

function validateAge(int $age): bool {
    return $age >= 1 && $age <= 60;
}

function validateWeight(float $weight): bool {
    return $weight > 0 && $weight < 200;
}

function validateHeight(float $height): bool {
    return $height > 10 && $height < 200;
}

function generateSecureToken(): string {
    return bin2hex(random_bytes(32));
}

function regenerateSession(): void {
    session_regenerate_id(true);
}

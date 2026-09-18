<?php
// Security configuration: CSRF, session, sanitization
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function sanitizeInput(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateAge(int $age): bool {
    return $age >= 0 && $age <= 60;
}

function validateWeight(float $weight): bool {
    return $weight >= 0.5 && $weight <= 50;
}

function validateHeight(float $height): bool {
    return $height >= 40 && $height <= 120;
}

function regenerateSession(): void {
    session_regenerate_id(true);
}

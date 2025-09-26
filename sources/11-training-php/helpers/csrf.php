<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token(): string {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): void {
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    echo '<input type="hidden" name="_csrf_token" value="' . $token . '">';
}

function csrf_validate(?string $token): bool {
    return !empty($token)
        && !empty($_SESSION['_csrf_token'])
        && hash_equals($_SESSION['_csrf_token'], $token);
}

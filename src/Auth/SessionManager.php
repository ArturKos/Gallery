<?php

declare(strict_types=1);

namespace ArturKos\Gallery\Auth;

/**
 * Wraps the PHP session for the gallery: secure cookie params, login/logout
 * with id regeneration, and a CSRF token bound to the session.
 *
 * State lives in `$_SESSION` so this class is intentionally not unit-tested;
 * the verbs delegate to PHP's session functions and are exercised end-to-end.
 */
final class SessionManager
{
    public function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict',
            'secure' => !empty($_SERVER['HTTPS']),
        ]);

        session_start();

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function login(string $username): void
    {
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = true;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public function isAuthenticated(): bool
    {
        return ($_SESSION['is_admin'] ?? false) === true;
    }

    public function username(): ?string
    {
        $username = $_SESSION['username'] ?? null;
        return is_string($username) ? $username : null;
    }

    public function csrfToken(): string
    {
        $token = $_SESSION['csrf_token'] ?? '';
        return is_string($token) ? $token : '';
    }

    public function verifyCsrfToken(string $token): bool
    {
        $expected = $_SESSION['csrf_token'] ?? '';
        if (!is_string($expected) || $expected === '') {
            return false;
        }
        return hash_equals($expected, $token);
    }
}

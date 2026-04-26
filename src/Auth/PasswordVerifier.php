<?php

declare(strict_types=1);

namespace ArturKos\Gallery\Auth;

/**
 * Verifies a username/password pair against a map of BCrypt hashes.
 *
 * The map shape matches the legacy `haslo.php` file: keys are usernames,
 * values are full BCrypt hashes produced by `password_hash`.
 */
final class PasswordVerifier
{
    private const DUMMY_HASH = '$2y$10$abcdefghijklmnopqrstuuMRcvbpgyl0jp7HF8YfeIB7gzG5p9aXi';

    /**
     * @param array<string, string> $credentials Map of username → BCrypt hash.
     */
    public function __construct(private readonly array $credentials)
    {
    }

    /**
     * Returns true when the plaintext password matches the stored hash for the user.
     *
     * Always runs a `password_verify` call — even when the username is unknown —
     * so timing differences do not leak which usernames exist.
     */
    public function verify(string $username, string $plaintext): bool
    {
        $hash = $this->credentials[$username] ?? null;

        if ($hash === null) {
            password_verify($plaintext, self::DUMMY_HASH);
            return false;
        }

        return password_verify($plaintext, $hash);
    }
}

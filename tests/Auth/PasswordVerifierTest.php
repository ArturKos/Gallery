<?php

declare(strict_types=1);

namespace ArturKos\Gallery\Tests\Auth;

use ArturKos\Gallery\Auth\PasswordVerifier;
use PHPUnit\Framework\TestCase;

final class PasswordVerifierTest extends TestCase
{
    public function testReturnsTrueForCorrectCredentials(): void
    {
        $verifier = new PasswordVerifier([
            'alice' => password_hash('correct horse battery staple', PASSWORD_BCRYPT),
        ]);

        self::assertTrue($verifier->verify('alice', 'correct horse battery staple'));
    }

    public function testReturnsFalseForWrongPassword(): void
    {
        $verifier = new PasswordVerifier([
            'alice' => password_hash('hunter2', PASSWORD_BCRYPT),
        ]);

        self::assertFalse($verifier->verify('alice', 'guessed-wrong'));
    }

    public function testReturnsFalseForUnknownUsername(): void
    {
        $verifier = new PasswordVerifier([
            'alice' => password_hash('hunter2', PASSWORD_BCRYPT),
        ]);

        self::assertFalse($verifier->verify('mallory', 'hunter2'));
    }

    public function testReturnsFalseForEmptyCredentialsTable(): void
    {
        $verifier = new PasswordVerifier([]);

        self::assertFalse($verifier->verify('alice', 'anything'));
    }
}

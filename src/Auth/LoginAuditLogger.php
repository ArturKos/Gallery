<?php

declare(strict_types=1);

namespace ArturKos\Gallery\Auth;

use DateTimeImmutable;
use RuntimeException;

/**
 * Append-only logger of successful logins. Each line records timestamp,
 * username, and the client IP address.
 */
final class LoginAuditLogger
{
    public function __construct(private readonly string $logFilePath)
    {
    }

    public function record(string $username, string $clientIp): void
    {
        $timestamp = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $line = sprintf("%s\t%s\t%s\n", $timestamp, $username, $clientIp);

        if (file_put_contents($this->logFilePath, $line, FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException("Could not write audit log: {$this->logFilePath}");
        }
    }
}

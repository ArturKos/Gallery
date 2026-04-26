<?php

declare(strict_types=1);

namespace ArturKos\Gallery;

/**
 * Path-traversal hardening for user-supplied directory navigation.
 *
 * The legacy code used `strstr($candidate, $base) !== false`, which a path
 * like `<base>/../../etc` defeats trivially. This class resolves both paths
 * with `realpath` and verifies the candidate sits inside the base directory.
 */
final class SafePath
{
    /**
     * Resolve `$candidate` to an absolute path contained within `$baseDirectory`.
     *
     * @return string|null The resolved absolute path, or null when the candidate
     *                     does not exist or escapes the base directory.
     */
    public static function resolveWithin(string $baseDirectory, string $candidate): ?string
    {
        $resolvedBase = realpath($baseDirectory);
        if ($resolvedBase === false) {
            return null;
        }

        $resolvedCandidate = realpath($candidate);
        if ($resolvedCandidate === false) {
            return null;
        }

        if ($resolvedCandidate === $resolvedBase) {
            return $resolvedCandidate;
        }

        if (str_starts_with($resolvedCandidate, $resolvedBase . DIRECTORY_SEPARATOR)) {
            return $resolvedCandidate;
        }

        return null;
    }
}

<?php

declare(strict_types=1);

/**
 * Escape a value for safe interpolation into HTML.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * URL-encode a relative path while keeping forward slashes readable.
 */
function url_path(string $relativePath): string
{
    return implode('/', array_map('rawurlencode', explode('/', $relativePath)));
}

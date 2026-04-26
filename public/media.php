<?php

declare(strict_types=1);

use ArturKos\Gallery\Auth\SessionManager;
use ArturKos\Gallery\SafePath;

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/credentials.php';

$session = new SessionManager();
$session->start();

if (!$session->isAuthenticated()) {
    http_response_code(403);
    exit;
}

$username = $session->username();
if ($username === null) {
    http_response_code(403);
    exit;
}

$relative = trim((string) ($_GET['path'] ?? ''), '/');
if ($relative === '') {
    http_response_code(404);
    exit;
}

$userDataRoot = $config['data_root'] . DIRECTORY_SEPARATOR . $username . DIRECTORY_SEPARATOR . 'data';
$candidate = $userDataRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
$resolved = SafePath::resolveWithin($userDataRoot, $candidate);

if ($resolved === null || !is_file($resolved)) {
    http_response_code(404);
    exit;
}

$mimeType = mime_content_type($resolved);
if ($mimeType === false) {
    $mimeType = 'application/octet-stream';
}

header('Content-Type: ' . $mimeType);
header('Content-Length: ' . (string) filesize($resolved));
header('X-Content-Type-Options: nosniff');
readfile($resolved);

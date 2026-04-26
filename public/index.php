<?php

declare(strict_types=1);

use ArturKos\Gallery\Auth\LoginAuditLogger;
use ArturKos\Gallery\Auth\PasswordVerifier;
use ArturKos\Gallery\Auth\SessionManager;
use ArturKos\Gallery\DirectoryBrowser;
use ArturKos\Gallery\MediaClassifier;
use ArturKos\Gallery\SafePath;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../templates/helpers.php';

$config = require __DIR__ . '/../config/credentials.php';

$session = new SessionManager();
$session->start();

$verifier = new PasswordVerifier($config['accounts']);
$auditLogger = new LoginAuditLogger($config['log_file_path']);

$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $submittedCsrfToken = (string) ($_POST['csrf_token'] ?? '');

    if (!$session->verifyCsrfToken($submittedCsrfToken)) {
        http_response_code(400);
        echo 'Invalid CSRF token.';
        exit;
    }

    if ($action === 'login') {
        $username = trim((string) ($_POST['login'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username !== '' && $verifier->verify($username, $password)) {
            $session->login($username);
            $auditLogger->record($username, $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            header('Location: /');
            exit;
        }

        $errorMessage = 'Login lub hasło nie pasuje.';
    } elseif ($action === 'logout') {
        $session->logout();
        header('Location: /');
        exit;
    }
}

if (!$session->isAuthenticated()) {
    $csrfToken = $session->csrfToken();
    require __DIR__ . '/../templates/login.php';
    exit;
}

$username = $session->username();
if ($username === null) {
    $session->logout();
    header('Location: /');
    exit;
}

$userDataRoot = $config['data_root'] . DIRECTORY_SEPARATOR . $username . DIRECTORY_SEPARATOR . 'data';
$requestedRelative = trim((string) ($_GET['directory'] ?? ''), '/');
$absoluteCandidate = $requestedRelative === ''
    ? $userDataRoot
    : $userDataRoot . DIRECTORY_SEPARATOR . $requestedRelative;

$resolvedDirectory = SafePath::resolveWithin($userDataRoot, $absoluteCandidate);
if ($resolvedDirectory === null) {
    $resolvedDirectory = realpath($userDataRoot);
    if ($resolvedDirectory === false) {
        http_response_code(500);
        echo 'Data directory is not configured for this user.';
        exit;
    }
    $requestedRelative = '';
}

$browser = new DirectoryBrowser(
    dataRoot: $userDataRoot,
    classifier: new MediaClassifier(),
    thumbnailDirectoryName: $config['thumbnail_directory_name'],
);

$subdirectories = $browser->listSubdirectories($resolvedDirectory);
$files = $browser->listFiles($resolvedDirectory);
$parentRelativePath = $browser->parentRelativePath($resolvedDirectory);
$currentRelativePath = $requestedRelative;
$csrfToken = $session->csrfToken();

require __DIR__ . '/../templates/gallery.php';

<?php

declare(strict_types=1);

use ArturKos\Gallery\MediaEntry;
use ArturKos\Gallery\MediaType;

/**
 * @var string           $username
 * @var string           $currentRelativePath
 * @var list<string>     $subdirectories
 * @var list<MediaEntry> $files
 * @var string|null      $parentRelativePath
 * @var string           $csrfToken
 */

$pageTitle = $currentRelativePath === ''
    ? 'Jesteś w katalogu głównym'
    : 'Jesteś w ' . $currentRelativePath;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Galeria zdjęć">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/components.css">
</head>
<body>
    <header id="header">
        <img src="/img/strona_domowa.gif" alt="Galeria" class="banner">
    </header>

    <nav id="menu">
        <dl>
            <dt>Wybierz kategorię:</dt>
            <?php foreach ($subdirectories as $subdirectory): ?>
                <?php
                $childRelative = $currentRelativePath === ''
                    ? $subdirectory
                    : $currentRelativePath . '/' . $subdirectory;
                ?>
                <dd>
                    <a href="?directory=<?= e(url_path($childRelative)) ?>">
                        <?= e($subdirectory) ?>
                    </a>
                </dd>
            <?php endforeach; ?>

            <?php if ($parentRelativePath !== null): ?>
                <dd class="back">
                    <a href="?directory=<?= e(url_path($parentRelativePath)) ?>">Wstecz</a>
                </dd>
            <?php endif; ?>
        </dl>
    </nav>

    <aside id="info">
        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <button type="submit" name="action" value="logout" class="logout">
                <img src="/img/wyloguj2.gif" alt="Wyloguj się" width="150">
            </button>
        </form>
    </aside>

    <main id="content">
        <?php foreach ($files as $file): ?>
            <?php $href = '/media.php?path=' . rawurlencode($file->relativePath); ?>
            <?php
            $thumbnailHref = $file->thumbnailRelativePath !== null
                ? '/media.php?path=' . rawurlencode($file->thumbnailRelativePath)
                : $href;
            ?>
            <?php if ($file->type === MediaType::Image): ?>
                <a class="thumbnail-link" href="<?= e($href) ?>" target="_blank" rel="noopener">
                    <img src="<?= e($thumbnailHref) ?>" alt="<?= e($file->name) ?>" width="100">
                </a>
            <?php elseif ($file->type === MediaType::Video): ?>
                <video controls width="200" preload="metadata">
                    <source src="<?= e($href) ?>">
                </video>
            <?php else: ?>
                <a class="download-link" href="<?= e($href) ?>" target="_blank" rel="noopener">
                    <?= e($file->name) ?>
                    [<?= e(number_format($file->sizeBytes / (1024 * 1024), 1)) ?> MiB]
                </a>
                <br>
            <?php endif; ?>
        <?php endforeach; ?>
    </main>

    <footer id="footer">
        <a href="/" aria-label="Wróć do katalogu głównego">
            <img class="banner" src="/img/cofnij.gif" alt="Wróć">
        </a>
    </footer>
</body>
</html>

<?php

declare(strict_types=1);

/**
 * @var string|null $errorMessage
 * @var string      $csrfToken
 */
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galeria — logowanie</title>
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/components.css">
</head>
<body>
    <main class="login">
        <?php if ($errorMessage !== null): ?>
            <p class="login__error"><?= e($errorMessage) ?></p>
        <?php endif; ?>

        <p>Aby kontynuować podaj login i hasło:</p>

        <form action="" method="post" class="login__form">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <input type="text" name="login" placeholder="Login" autocomplete="username" required>
            <input type="password" name="password" placeholder="Hasło" autocomplete="current-password" required>
            <button type="submit" name="action" value="login">Zaloguj się</button>
        </form>

        <img src="/img/brein_animated.gif" alt="" class="login__decoration" width="200">
    </main>
</body>
</html>

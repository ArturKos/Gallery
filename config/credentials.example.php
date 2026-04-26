<?php

declare(strict_types=1);

/*
 * Copy this file to credentials.php and fill in real values.
 * credentials.php is gitignored.
 *
 * Generate a BCrypt hash:
 *   php -r "echo password_hash('your_password', PASSWORD_BCRYPT) . PHP_EOL;"
 */

return [
    'accounts' => [
        // 'username' => '$2y$10$...bcrypt-hash...',
    ],
    'data_root' => __DIR__ . '/../accounts',
    'thumbnail_directory_name' => 'thumbnails',
    'log_file_path' => __DIR__ . '/../log.txt',
];

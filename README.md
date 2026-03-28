# Gallery

A PHP-based web image gallery with **BCrypt authentication**, session management, directory browsing, thumbnail support, and inline video playback. Users log in to browse their personal file collections organized in folders, with support for images, videos, and downloadable files.

![PHP](https://img.shields.io/badge/PHP-7.x%2B-777BB4?logo=php&logoColor=white)
![HTML](https://img.shields.io/badge/HTML-XHTML%201.0-E34F26?logo=html5&logoColor=white)
![CSS](https://img.shields.io/badge/CSS-3-1572B6?logo=css3&logoColor=white)
![Auth](https://img.shields.io/badge/Auth-BCrypt-green)

## Features

- **BCrypt password authentication** using PHP's `password_hash()` and `password_verify()` for secure credential handling
- **Session-based access control** with login/logout, session regeneration on logout, and URL path validation to prevent directory traversal
- **Per-user file isolation** where each user can only access files within their own `konta/<username>/data/` directory
- **Recursive directory browsing** with dynamically generated navigation links and a back button for folder traversal
- **Thumbnail support** displaying smaller preview images from a `miniatury/` subdirectory when available, falling back to resized originals
- **Image format support** for JPG, JPEG, TIFF, BMP, GIF, and WMF files displayed as clickable thumbnails
- **Inline video playback** with HTML5 `<video>` controls for MP4 and MKV files
- **Generic file downloads** for non-media files showing filename and size in MiB
- **Login audit logging** recording timestamp, username, and IP address to a `log.txt` file
- **CSS-based layout** with a sidebar category menu, header banner, content area, and footer navigation

## Screenshots

![Login Screen](https://user-images.githubusercontent.com/17749811/152383401-26184b87-5e4b-4810-9544-74e379cc99d9.png)

![Gallery View](https://user-images.githubusercontent.com/17749811/152383422-eb2b4ba5-66f7-4c01-9193-e7847422f0ed.png)

## Dependencies

| Dependency | Version | Purpose |
|------------|---------|---------|
| PHP | >= 7.0 | Server-side scripting with BCrypt support |
| Apache / Nginx | any | Web server with PHP module |

No external PHP libraries or frameworks are required. The application uses only built-in PHP functions.

## Setup

### 1. Deploy Files

Copy all project files to your web server document root:

```bash
cp -r Gallery/ /var/www/html/gallery/
```

### 2. Configure Users

Edit `haslo.php` to add user accounts. Passwords must be BCrypt hashes:

```php
<?php
return [
    'username' => '$2y$10$...'  // Generate with: php -r "echo password_hash('password', PASSWORD_BCRYPT);"
];
```

Generate a BCrypt hash for a password:

```bash
php -r "echo password_hash('your_password', PASSWORD_BCRYPT) . PHP_EOL;"
```

### 3. Create User Directories

For each user, create a data directory:

```bash
mkdir -p konta/username/data
```

Place images, videos, and other files in subdirectories under `data/`. Optionally create a `miniatury/` folder inside each directory containing thumbnail versions of the images.

### 4. Set Permissions

```bash
chown -R www-data:www-data /var/www/html/gallery/
chmod -R 755 /var/www/html/gallery/
touch log.txt && chmod 666 log.txt
```

## Project Structure

```
Gallery/
├── README.md                   # This file
├── index.php                   # Main entry point: login form, session handling, gallery rendering
├── func.php                    # Helper functions: file/directory search, login audit logging
├── haslo.php                   # User credentials (username -> BCrypt hash map)
├── css/
│   ├── style.css               # Layout: sidebar menu, definition lists, link styles
│   └── strona.css              # Page structure: header, menu, content, footer positioning
├── obrazy/                     # UI assets
│   ├── strona_domowa.gif       # Header banner
│   ├── brein_animated.gif      # Login screen animation
│   ├── wyloguj.gif             # Logout button image
│   ├── wyloguj2.gif            # Logout button image (alternate)
│   ├── cofnij.gif              # Back/home button image
│   ├── certyfikat.gif          # SSL certificate link image
│   └── ...                     # Additional UI graphics
└── konta/                      # Per-user file storage (not tracked in repo)
    └── <username>/
        └── data/
            ├── subfolder/
            │   ├── image.jpg
            │   └── miniatury/  # Optional thumbnail directory
            │       └── image.jpg
            └── video.mp4
```

## Security Notes

- Passwords are stored as BCrypt hashes, never in plain text
- Session IDs are regenerated on logout to prevent session fixation
- Directory traversal is blocked by validating that requested paths contain the user's base directory
- The `log.txt` file records all successful logins with timestamps and IP addresses for audit purposes

## License

This project is provided as-is for educational purposes.

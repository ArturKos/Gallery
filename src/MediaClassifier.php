<?php

declare(strict_types=1);

namespace ArturKos\Gallery;

/**
 * Classifies a filename into the rendering category used by the gallery view.
 */
final class MediaClassifier
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tif', 'tiff', 'wmf', 'webp'];
    private const VIDEO_EXTENSIONS = ['mp4', 'mkv', 'webm'];

    public function classify(string $filename): MediaType
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($extension, self::IMAGE_EXTENSIONS, true)) {
            return MediaType::Image;
        }

        if (in_array($extension, self::VIDEO_EXTENSIONS, true)) {
            return MediaType::Video;
        }

        return MediaType::Download;
    }
}

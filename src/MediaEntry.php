<?php

declare(strict_types=1);

namespace ArturKos\Gallery;

/**
 * Immutable record describing a single file entry rendered by the gallery view.
 */
final class MediaEntry
{
    public function __construct(
        public readonly string $name,
        public readonly string $relativePath,
        public readonly MediaType $type,
        public readonly int $sizeBytes,
        public readonly ?string $thumbnailRelativePath,
    ) {
    }
}

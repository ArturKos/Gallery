<?php

declare(strict_types=1);

namespace ArturKos\Gallery;

use DirectoryIterator;
use RuntimeException;

/**
 * Lists subdirectories and media files inside a user's data root.
 *
 * All returned paths are *relative* to the data root so the front controller
 * can build URLs without leaking absolute filesystem locations.
 */
final class DirectoryBrowser
{
    public function __construct(
        private readonly string $dataRoot,
        private readonly MediaClassifier $classifier,
        private readonly string $thumbnailDirectoryName = 'thumbnails',
    ) {
    }

    /**
     * @return list<string> Subdirectory names (one segment, not a full path),
     *                      excluding the thumbnail folder.
     */
    public function listSubdirectories(string $absoluteDirectory): array
    {
        $subdirectories = [];
        foreach ($this->iterate($absoluteDirectory) as $entry) {
            if ($entry->isDot() || !$entry->isDir()) {
                continue;
            }
            if ($entry->getFilename() === $this->thumbnailDirectoryName) {
                continue;
            }
            $subdirectories[] = $entry->getFilename();
        }
        sort($subdirectories);
        return $subdirectories;
    }

    /**
     * @return list<MediaEntry>
     */
    public function listFiles(string $absoluteDirectory): array
    {
        $entries = [];
        foreach ($this->iterate($absoluteDirectory) as $entry) {
            if ($entry->isDot() || !$entry->isFile()) {
                continue;
            }

            $name = $entry->getFilename();
            $absolutePath = $entry->getPathname();
            $relativePath = $this->toRelative($absolutePath);
            $thumbnail = $this->findThumbnail($absoluteDirectory, $name);

            $entries[] = new MediaEntry(
                name: $name,
                relativePath: $relativePath,
                type: $this->classifier->classify($name),
                sizeBytes: (int) $entry->getSize(),
                thumbnailRelativePath: $thumbnail,
            );
        }

        usort($entries, static fn (MediaEntry $a, MediaEntry $b) => strcmp($a->name, $b->name));
        return $entries;
    }

    /**
     * Computes the parent directory relative to the data root, or null when
     * the caller is already at the root.
     */
    public function parentRelativePath(string $absoluteDirectory): ?string
    {
        $resolvedRoot = realpath($this->dataRoot);
        $resolved = realpath($absoluteDirectory);
        if ($resolvedRoot === false || $resolved === false || $resolved === $resolvedRoot) {
            return null;
        }
        $parent = dirname($resolved);
        if ($parent === $resolvedRoot) {
            return '';
        }
        return $this->toRelative($parent);
    }

    private function iterate(string $absoluteDirectory): DirectoryIterator
    {
        if (!is_dir($absoluteDirectory)) {
            throw new RuntimeException("Not a directory: {$absoluteDirectory}");
        }
        return new DirectoryIterator($absoluteDirectory);
    }

    private function toRelative(string $absolutePath): string
    {
        $resolvedRoot = realpath($this->dataRoot);
        if ($resolvedRoot === false) {
            return $absolutePath;
        }
        if (str_starts_with($absolutePath, $resolvedRoot . DIRECTORY_SEPARATOR)) {
            return substr($absolutePath, strlen($resolvedRoot) + 1);
        }
        return $absolutePath;
    }

    private function findThumbnail(string $absoluteDirectory, string $filename): ?string
    {
        $thumbnailPath = $absoluteDirectory . DIRECTORY_SEPARATOR
            . $this->thumbnailDirectoryName . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($thumbnailPath)) {
            return null;
        }
        return $this->toRelative($thumbnailPath);
    }
}

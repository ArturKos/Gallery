<?php

declare(strict_types=1);

namespace ArturKos\Gallery\Tests\Gallery;

use ArturKos\Gallery\SafePath;
use PHPUnit\Framework\TestCase;

final class SafePathTest extends TestCase
{
    private string $baseDirectory;

    protected function setUp(): void
    {
        $this->baseDirectory = sys_get_temp_dir() . '/safepath_' . bin2hex(random_bytes(6));
        mkdir($this->baseDirectory . '/inside/deep', 0700, true);
        mkdir(sys_get_temp_dir() . '/outside_' . bin2hex(random_bytes(6)));
    }

    protected function tearDown(): void
    {
        $this->removeRecursively($this->baseDirectory);
    }

    public function testResolvesPathsInsideTheBase(): void
    {
        $resolved = SafePath::resolveWithin($this->baseDirectory, $this->baseDirectory . '/inside/deep');
        self::assertSame(realpath($this->baseDirectory . '/inside/deep'), $resolved);
    }

    public function testAcceptsTheBaseDirectoryItself(): void
    {
        $resolved = SafePath::resolveWithin($this->baseDirectory, $this->baseDirectory);
        self::assertSame(realpath($this->baseDirectory), $resolved);
    }

    public function testRejectsTraversalEscapingTheBase(): void
    {
        $escapeAttempt = $this->baseDirectory . '/inside/../../..';
        self::assertNull(SafePath::resolveWithin($this->baseDirectory, $escapeAttempt));
    }

    public function testRejectsAbsolutePathOutsideTheBase(): void
    {
        self::assertNull(SafePath::resolveWithin($this->baseDirectory, '/etc'));
    }

    public function testRejectsNonExistentCandidate(): void
    {
        self::assertNull(SafePath::resolveWithin($this->baseDirectory, $this->baseDirectory . '/does-not-exist'));
    }

    public function testRejectsNonExistentBase(): void
    {
        self::assertNull(SafePath::resolveWithin('/nonexistent-base-' . bin2hex(random_bytes(4)), $this->baseDirectory));
    }

    public function testDoesNotMatchSiblingWithCommonPrefix(): void
    {
        $sibling = $this->baseDirectory . '_sibling';
        mkdir($sibling);

        try {
            self::assertNull(SafePath::resolveWithin($this->baseDirectory, $sibling));
        } finally {
            rmdir($sibling);
        }
    }

    private function removeRecursively(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }
        if (is_file($path) || is_link($path)) {
            unlink($path);
            return;
        }
        foreach (scandir($path) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $this->removeRecursively($path . '/' . $entry);
        }
        rmdir($path);
    }
}

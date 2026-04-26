<?php

declare(strict_types=1);

namespace ArturKos\Gallery\Tests\Gallery;

use ArturKos\Gallery\DirectoryBrowser;
use ArturKos\Gallery\MediaClassifier;
use ArturKos\Gallery\MediaType;
use PHPUnit\Framework\TestCase;

final class DirectoryBrowserTest extends TestCase
{
    private string $dataRoot;

    protected function setUp(): void
    {
        $this->dataRoot = sys_get_temp_dir() . '/browser_' . bin2hex(random_bytes(6));
        mkdir($this->dataRoot . '/holiday/thumbnails', 0700, true);
        mkdir($this->dataRoot . '/empty', 0700, true);

        file_put_contents($this->dataRoot . '/holiday/photo.jpg', 'fake-image');
        file_put_contents($this->dataRoot . '/holiday/thumbnails/photo.jpg', 'fake-thumb');
        file_put_contents($this->dataRoot . '/holiday/clip.mp4', 'fake-video');
        file_put_contents($this->dataRoot . '/holiday/notes.pdf', 'fake-doc');
    }

    protected function tearDown(): void
    {
        $this->removeRecursively($this->dataRoot);
    }

    public function testListsSubdirectoriesAlphabeticallyExcludingThumbnails(): void
    {
        $browser = $this->makeBrowser();
        self::assertSame(['empty', 'holiday'], $browser->listSubdirectories($this->dataRoot));
    }

    public function testHidesThumbnailFolderFromSubdirectoryListing(): void
    {
        $browser = $this->makeBrowser();
        self::assertNotContains('thumbnails', $browser->listSubdirectories($this->dataRoot . '/holiday'));
    }

    public function testListsFilesAndAttachesThumbnailsForImages(): void
    {
        $browser = $this->makeBrowser();
        $files = $browser->listFiles($this->dataRoot . '/holiday');

        self::assertCount(3, $files);
        $byName = [];
        foreach ($files as $entry) {
            $byName[$entry->name] = $entry;
        }

        self::assertSame(MediaType::Image, $byName['photo.jpg']->type);
        self::assertSame('holiday/thumbnails/photo.jpg', $byName['photo.jpg']->thumbnailRelativePath);

        self::assertSame(MediaType::Video, $byName['clip.mp4']->type);
        self::assertNull($byName['clip.mp4']->thumbnailRelativePath);

        self::assertSame(MediaType::Download, $byName['notes.pdf']->type);
    }

    public function testParentRelativePathIsNullAtRoot(): void
    {
        $browser = $this->makeBrowser();
        self::assertNull($browser->parentRelativePath($this->dataRoot));
    }

    public function testParentRelativePathReturnsEmptyStringOneLevelDeep(): void
    {
        $browser = $this->makeBrowser();
        self::assertSame('', $browser->parentRelativePath($this->dataRoot . '/holiday'));
    }

    private function makeBrowser(): DirectoryBrowser
    {
        return new DirectoryBrowser($this->dataRoot, new MediaClassifier());
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

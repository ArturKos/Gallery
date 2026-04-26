<?php

declare(strict_types=1);

namespace ArturKos\Gallery\Tests\Gallery;

use ArturKos\Gallery\MediaClassifier;
use ArturKos\Gallery\MediaType;
use PHPUnit\Framework\TestCase;

final class MediaClassifierTest extends TestCase
{
    /**
     * @return iterable<string, array{string, MediaType}>
     */
    public static function classificationCases(): iterable
    {
        yield 'jpeg uppercase' => ['photo.JPG', MediaType::Image];
        yield 'png' => ['art.png', MediaType::Image];
        yield 'gif' => ['banner.gif', MediaType::Image];
        yield 'tiff' => ['scan.tiff', MediaType::Image];
        yield 'mp4' => ['clip.mp4', MediaType::Video];
        yield 'mkv' => ['film.MKV', MediaType::Video];
        yield 'webm' => ['short.webm', MediaType::Video];
        yield 'pdf falls through to download' => ['notes.pdf', MediaType::Download];
        yield 'no extension falls through to download' => ['readme', MediaType::Download];
        yield 'unknown extension' => ['archive.7z', MediaType::Download];
    }

    /**
     * @dataProvider classificationCases
     */
    public function testClassifiesByExtension(string $filename, MediaType $expected): void
    {
        self::assertSame($expected, (new MediaClassifier())->classify($filename));
    }
}

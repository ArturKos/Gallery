<?php

declare(strict_types=1);

namespace ArturKos\Gallery;

enum MediaType
{
    case Image;
    case Video;
    case Download;
}

<?php

namespace app\models\show;

/** 1:1 mapping to 'show' DB table */
final readonly class DemoSong
{
    public function __construct(
        public int $id,
        public int $artistId,
        public string $title,
        public string $songUrl,
        public string $pictureUrl,
        public bool $isActive
    ) {
    }
}
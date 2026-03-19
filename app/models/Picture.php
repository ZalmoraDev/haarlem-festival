<?php

namespace app\models;

/** 1:1 mapping to 'picture' DB table */
final readonly class Picture
{
    public function __construct(
        public int $id,
        public int $dataObjectId,
        public int $pictureUrl,
        public string $altText,
        public string $hoverText,
        public bool $isActive
    )
    {
    }
}
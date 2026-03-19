<?php

namespace app\models\show;

use app\models\DataObject;

/** 1:1 mapping to 'artist' DB table */
final readonly class Artist extends DataObject
{
    public function __construct(
        public int $id,
        public string $careerHighlightDescription
    )
    {
    }
}
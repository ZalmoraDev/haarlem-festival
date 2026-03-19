<?php

namespace app\models\show;

/** 1:1 mapping to 'line_up' DB table */
final readonly class LineUp
{
    public function __construct(
        public int $showId,
        public int $artistId,
    )
    {
    }
}
<?php

namespace app\models\show;

use DateTime;

/** 1:1 mapping to 'show' DB table */
final readonly class Show
{
    public function __construct(
        public int $id,
        public int $venueId,
        public string $name,
        public DateTime $dateTime,
        public int $availableSpots,
        public float $price,
        public bool $isActive,
    )
    {
    }
}
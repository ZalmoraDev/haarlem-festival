<?php

namespace app\models\yummy;
use DateTimeImmutable;

/** 1:1 mapping to 'time_slot' DB table */
final readonly class TimeSlot
{
    public function __construct(
        public int $id,
        public int $restaurantId,
        public DateTimeImmutable $startDateTime,
        public DateTimeImmutable $endDateTime,
        public bool $isActive
    )
    {
    }
}
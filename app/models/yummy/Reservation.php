<?php

namespace app\models\yummy;

use DateTimeImmutable;

/** 1:1 mapping to 'reservation' DB table */
final readonly class Reservation
{
    public function __construct(
        public int $id,
        public int $timeSlotId,
        public DateTimeImmutable $createdAt,
        public int $AmountOfChildren,
        public int $AmountOfAdults
    )
    {
    }
}
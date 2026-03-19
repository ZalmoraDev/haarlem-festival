<?php

namespace app\models\history;

/** 1:1 mapping to 'tour_ticket' DB table */
final readonly class TourTicket
{
    public function __construct(
        public int $id,
        public int $tourId,
    )
    {
    }
}
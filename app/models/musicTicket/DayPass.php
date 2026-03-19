<?php

namespace app\models\music;

use app\models\music\MusicTicket;
use DateTime;

/** 1:1 mapping to 'day_pass' DB table */
final readonly class DayPass extends MusicTicket
{
    public function __construct(
        public int $id,
        public DateTime $date,
    )
    {
    }
}
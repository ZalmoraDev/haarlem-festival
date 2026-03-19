<?php

namespace app\models\music;

/** 1:1 mapping to 'single_ticket' DB table */
final readonly class Singleticket extends MusicTicket
{
    public function __construct(
        public int $id,
        public int $showId,
    )
    {
    }
}
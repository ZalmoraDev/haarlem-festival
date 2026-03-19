<?php

namespace app\models\music;
use app\models\enums\Genre;
use app\models\Ticket;

/** 1:1 mapping to 'music_ticket' DB table */
readonly class MusicTicket extends Ticket
{
    public function __construct(
        public int $id,
        public Genre $genre,
    )
    {
    }
}
<?php

namespace app\models\music;

use app\models\music\MusicTicket;


/** 1:1 mapping to 'access_pass' DB table */
final readonly class AccessPass extends MusicTicket
{
    public function __construct(
        public int $id,
    )
    {
    }
}
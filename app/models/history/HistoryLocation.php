<?php

namespace app\models\history;

use app\models\DataObject;

/** 1:1 mapping to 'history_location' DB table */
final readonly class HistoryLocation extends DataObject
{
    public function __construct(
        public int $id,
        public int $addressId,
        public int $buildYear,
    )
    {
    }
}
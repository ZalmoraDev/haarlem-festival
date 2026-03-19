<?php

namespace app\models\show;

use app\models\DataObject;
use Symfony\Component\Validator\Tests\Constraints\Data;

/** 1:1 mapping to 'venue' DB table */
final readonly class Venue extends DataObject
{
    public function __construct(
        public int $id,
        public int $addressId,
    )
    {
    }
}
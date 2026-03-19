<?php

namespace app\models\history;

use DateTime;
use DateTimeImmutable;
use SplDoublyLinkedList;
use Symfony\Component\Validator\Constraints\Language;

/** 1:1 mapping to 'tour' DB table */
final readonly class Tour
{
    public function __construct(
        public int               $id,
        public DateTimeImmutable $date,
        public Language          $language,
        public int              $availableSpots,
        public float           $price,
        public bool              $isActive
    )
    {
    }
}
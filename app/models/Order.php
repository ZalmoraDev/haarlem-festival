<?php

namespace app\models;

use DateTime;
use app\models\enums\OrderStatus;

/** 1:1 mapping to 'order' DB table */
final readonly class Order
{
    public function __construct(
        public int $id,
        public int $userId,
        public DateTime $orderDatetime,
        public OrderStatus $status,
        public bool $isActive,
        //public bool $isPaid,?
        public float $totalCost
    )
    {
    }
}
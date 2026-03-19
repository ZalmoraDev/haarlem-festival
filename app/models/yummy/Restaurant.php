<?php

namespace app\models\yummy;
use app\models\enums\Cuisine;
use app\models\DataObject;

/** 1:1 mapping to 'restaurant' DB table */
final readonly class Restaurant extends DataObject
{    public function __construct(
        public int $id,
        public int $addressId,
        public string $phone,
        public string $email,
        public int $starRating,
        public float $priceAdult,
        public float $priceChild,
        public int $availableSeats,
        public Cuisine $cuisine,
        public float $reservationFee
    )
    {
    }
}
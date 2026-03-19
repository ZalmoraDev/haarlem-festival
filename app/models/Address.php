<?php

namespace app\models;

/** 1:1 mapping to 'addresses' DB table */
final class Address
{
    public function __construct(
        public int     $addressId,
        public string  $streetAddress,
        public string  $streetNumber, // string not int, can have optional suffix like 12A
        public ?string $apartmentSuite, // Optional, apartment or suite number in flats.
        public string  $city,
        public string  $postalCode,
    )
    {
    }
}
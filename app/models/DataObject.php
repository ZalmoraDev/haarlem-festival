<?php

namespace app\models;

/** 1:1 mapping to 'data_object' DB table */
readonly class DataObject
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public bool $isActive
    )
    {
    }
}   
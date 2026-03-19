<?php

namespace app\models;

/** 1:1 mapping to 'ticket' DB table */
readonly class Ticket
{
    public function __construct(
        public int $id,
        public int $orderId,
        public int $employeeId,
        public string $QRCode,
        public string $isScanned,
        public float $ticketCost,
        public bool $isActive
    )
    {
    }
}
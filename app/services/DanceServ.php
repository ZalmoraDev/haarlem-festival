<?php


namespace app\services;

use app\repositories\interfaces\IDanceRepo;
use app\services\interfaces\IDanceServ;

final readonly class DanceServ implements IDanceServ
{
    private IDanceRepo $IDanceRepo;

    public function __construct(IDanceRepo $IDanceRepo)
    {
        $this->IDanceRepo = $IDanceRepo;
    }
}
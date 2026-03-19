<?php


namespace app\services;

use app\repositories\interfaces\IJazzRepo;
use app\services\interfaces\IJazzServ;

final readonly class JazzServ implements IJazzServ
{
    private IJazzRepo $IJazzRepo;

    public function __construct(IJazzRepo $IJazzRepo)
    {
        $this->IJazzRepo = $IJazzRepo;
    }
}
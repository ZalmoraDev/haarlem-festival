<?php


namespace app\services;

use app\repositories\interfaces\IYummyRepo;
use app\services\interfaces\IYummyServ;

final readonly class YummyServ implements IYummyServ
{
    private IYummyRepo $IYummyRepo;

    public function __construct(IYummyRepo $IYummyRepo)
    {
        $this->IYummyRepo = $IYummyRepo;
    }
}
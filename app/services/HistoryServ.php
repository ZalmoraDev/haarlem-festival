<?php


namespace app\services;

use app\repositories\interfaces\IHistoryRepo;
use app\services\interfaces\IHistoryServ;

final readonly class HistoryServ implements IHistoryServ
{
    private IHistoryRepo $IHistoryRepo;

    public function __construct(IHistoryRepo $IHistoryRepo)
    {
        $this->IHistoryRepo = $IHistoryRepo;
    }
}
<?php

namespace app\controllers;

use app\core\View;
use app\models\enums\PageIndex;
use app\services\interfaces\IHistoryServ;


/** Controller for History theme actions
 * - GET: Display history homepage
 * - POST: - */
final readonly class HistoryCtrl extends BaseCtrl
{
    private IHistoryServ $IHistoryServ;

    public function __construct(IHistoryServ $IHistoryServ)
    {
        $this->IHistoryServ = $IHistoryServ;
    }

    //region GET Requests

    /** GET /history, Landingpage for history page */
    public function homePage(): void
    {
        View::render('/history/home.php', "History" . View::addSiteName(), PageIndex::HistoryHome->value);
    }
    //endregion GET Requests


    //region POST Requests

    //endregion POST Requests
}
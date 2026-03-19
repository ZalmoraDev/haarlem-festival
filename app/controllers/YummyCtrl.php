<?php

namespace app\controllers;

use app\core\View;
use app\models\enums\PageIndex;
use app\services\interfaces\IYummyServ;


/** Controller for Yummy theme actions
 * - GET: Display history homepage
 * - POST: - */
final readonly class YummyCtrl extends BaseCtrl
{
    private IYummyServ $IYummyServ;

    public function __construct(IYummyServ $IYummyServ)
    {
        $this->IYummyServ = $IYummyServ;
    }

    //region GET Requests

    /** GET /yummy, Landingpage for yummy page */
    public function homePage(): void
    {
        View::render('/yummy/home.php', "YUMMY!" . View::addSiteName(), PageIndex::YummyHome->value);
    }
    //endregion GET Requests


    //region POST Requests

    //endregion POST Requests
}
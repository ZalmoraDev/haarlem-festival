<?php

namespace app\controllers;

use app\core\View;
use app\models\enums\PageIndex;
use app\services\interfaces\IDanceServ;

/** Controller for DANCE! theme actions
 * - GET: Display dance homepage
 * - POST: - */
final readonly class DanceCtrl extends BaseCtrl
{
    private IDanceServ $IDanceServ;

    public function __construct(IDanceServ $IDanceServ)
    {
        $this->IDanceServ = $IDanceServ;
    }

    //region GET Requests

    /** GET /dance, Landingpage for dance page */
    public function homePage(): void
    {
        View::render('/dance/home.php', "DANCE! " . View::addSiteName(), PageIndex::DanceHome->value);
    }
    //endregion GET Requests


    //region POST Requests

    //endregion POST Requests
}
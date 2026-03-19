<?php
namespace app\controllers;

use app\core\View;
use app\models\enums\PageIndex;

/** Controller to handle GET error pages
 * - GET: display 404/405 pages */
final readonly class ErrorCtrl extends BaseCtrl
{
    //region GET Requests
    /** 404 Not Found */
    public function notFound(): void
    {
        http_response_code(404);
        View::render('error/404.html', "404 Not Found", PageIndex::Error404->value);
    }

    /** 405 Method Not Allowed */
    public function methodNotAllowed(): void
    {
        http_response_code(405);
        View::render('error/404.html', "405 Not Allowed", PageIndex::Error405->value);
    }
    //endregion
}
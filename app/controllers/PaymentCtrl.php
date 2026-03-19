<?php

namespace app\controllers;

use app\core\View;
use app\models\enums\PageIndex;
use app\services\interfaces\IPaymentServ;
use app\services\exceptions\BaseServExc;



/** 
 * Controller for Payment theme actions
 * 
 * - GET: Display payment homepage and checkout page
 * - POST: Handle the checkout process and webhook events */
final readonly class PaymentCtrl extends BaseCtrl
{
    private IPaymentServ $IPaymentServ;

    public function __construct(IPaymentServ $IPaymentServ)
    {
        $this->IPaymentServ = $IPaymentServ;
    }


    //region GET Requests

    /**
     * Display the payment landing page.
     * 
     * Route: GET /payment
     * @return void
     */

    public function homePage(): void
    {
        View::render('/payment/paymentPage.php', "Payment" . View::addSiteName(), PageIndex::PaymentHome->value);
    }

    /** Display the checkout page.
     * 
     * Route: GET /payment/checkout
     * @return void
     */
    public function checkoutPage(): void
    {
        View::render('/payment/checkout.php', "Payment" . View::addSiteName(), PageIndex::PaymentCheckout->value);
    }

    //endregion GET Requests


    //region POST Requests

    public function checkout(): void
    {
        $url = $this->IPaymentServ->checkout();
        $this->redirect($url);
    }

    public function handleWebhook(): void
    {
        $this->IPaymentServ->handleWebhook();
    }
    
    //endregion POST Requests
}

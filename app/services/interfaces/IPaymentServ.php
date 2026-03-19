<?php

namespace app\services\interfaces;

interface IPaymentServ
{ 
    /**
     * Initiates the checkout process by creating a Stripe Checkout Session and returns the session URL for redirection.
     *
     * @return string
     */
    public function checkout(): string;

    /**
     * Handles Stripe webhook events, such as payment success or failure, to update payment status in the database.
     *
     * @return void
     */
    public function handleWebhook(): void;

}
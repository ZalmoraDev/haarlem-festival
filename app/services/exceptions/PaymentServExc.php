<?php

namespace app\services\exceptions;

final class PaymentServExc extends BaseServExc
{


    // webhook errors
    public const string INVALID_PAYLOAD = 'Invalid payload received from Stripe webhook.';

    public const string INVALID_SIGNATURE = 'Stripe signature verification failed.';

    // user errors
    public const string CARD_ERROR = 'There was an error with the card provided.';

    public const string RATE_LIMIT = 'Server is busy. Please try again in a moment.';

    // system errors
    public const string API_ERROR = 'Stripe API returned an error. Please try again later.';

    //generic error for unexpected exceptions
    public const string GENERIC_STRIPE_ERROR = 'An error occurred while processing the payment.';
}

<?php


namespace app\services;

use app\repositories\interfaces\IPaymentRepo;
use app\services\interfaces\IPaymentServ;
use app\services\exceptions\PaymentServExc;
use Stripe\Stripe;

/**
 * Service layer for handling payment-related business logic.
 */
final readonly class PaymentServ implements IPaymentServ
{
    private IPaymentRepo $IPaymentRepo;


    public function __construct(IPaymentRepo $IPaymentRepo)
    {
        $this->IPaymentRepo = $IPaymentRepo;
    }


    public function checkout(): string
    {

        $baseUrl = getenv("SITE_URL");
        $stripe_secret_key = getenv("STRIPE_SECRET_KEY");

        try {
            Stripe::setApiKey($stripe_secret_key);

            //TODO: Replace the hardcoded line items with dynamic data from the user's cart or order details
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'T-shirt',
                        ],
                        'unit_amount' => 2000,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $baseUrl . '/payment/checkout',
                'cancel_url' => $baseUrl . '/history'
            ]);
            return $checkout_session->url;
        } 
        catch (\Stripe\Exception\CardException $e) {
            throw new PaymentServExc(PaymentServExc::CARD_ERROR, 402, previous: $e);
        } catch (\Stripe\Exception\RateLimitException $e) {
            throw new PaymentServExc(PaymentServExc::RATE_LIMIT, 429, previous: $e);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            throw new PaymentServExc(PaymentServExc::API_ERROR, 500, previous: $e);
        } catch (\Exception $e) {
            throw new PaymentServExc(PaymentServExc::GENERIC_STRIPE_ERROR, 500, previous: $e);
        }
    }

    public function handleWebhook(): void
    {
        $stripe_secret_key = getenv("STRIPE_SECRET_KEY");
        $endpoint_secret = getenv("STRIPE_WEBHOOK_SECRET");

        Stripe::setApiKey($stripe_secret_key);

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\Stripe\Exception\UnexpectedValueException $e) {
            throw new PaymentServExc(PaymentServExc::INVALID_PAYLOAD, 400, previous: $e);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            throw new PaymentServExc(PaymentServExc::INVALID_SIGNATURE, 400, previous: $e);
        } catch (\Exception $e) {
            throw new PaymentServExc(PaymentServExc::GENERIC_STRIPE_ERROR, 500, previous: $e);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                break;
            default:
            break;
        }


        //TODO: Update payment status in the database using the session information
        // $this->IPaymentRepo->updatePaymentStatus($session);

    
    }
}

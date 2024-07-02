<?php


namespace App\Helper\Payment;


use Stripe\StripeClient;
use Stripe\Webhook;

class StripeHelper
{
    private static function connect()
    {
        return new StripeClient(env('STRIPE_KEY'));

    }

    public static function orderDataModel($price, $title, $quantity)
    {
        return [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount_decimal' => intval($price * 100),
                'product_data' => [
                    'name' => $title,
                ],
            ],
            'quantity' => $quantity
        ];
    }

    public static function createSession($orderItems)
    {

        $stripe = self::connect();
        $sessionInfo = [
            'payment_method_types' => ['card'],
            'line_items' => $orderItems,
            'mode' => 'payment',
            'expires_at' => now()->addMinutes(90)->timestamp
        ];
        if (env('APP_ENV') === 'local') {
            // $sessionInfo['success_url'] = 'http://localhost:3000/payment/checkout/order/success';
            $sessionInfo['success_url'] = 'http://localhost:3000/thankyou';
            $sessionInfo['cancel_url'] = 'http://localhost:3000/payment/checkout/order/failed?session_id={CHECKOUT_SESSION_ID}';
        } else {
            $sessionInfo['success_url'] = 'https://new.sevendisplays.com/thankyou';
            // $sessionInfo['success_url'] = 'https://prod.sevendisplays.com/payment/checkout/order/success';
            $sessionInfo['cancel_url'] = 'https://new.sevendisplays.com/payment/checkout/order/failed?session_id={CHECKOUT_SESSION_ID}';
        }
        return $stripe->checkout->sessions->create($sessionInfo);
    }

    public static function verifySession($payload, $sig_header)
    {
        $endpoint_secret = env('WEBHOOK_SECRET');
        $event = Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret);
        return $event;
    }

    public static function retrievePaymentIntent($paymentIntent)
    {
        $stripe = self::connect();
        $payment = $stripe->paymentIntents->retrieve(
            $paymentIntent,
            []
        );
        return $payment;
    }

    public static function cancelPaymentIntent($sessionId)
    {
        $stripe = self::connect();
        return $stripe->checkout->sessions->expire(
            $sessionId,
            []
        );
//        $stripe->checkout->sessions->all(
//            ['limit'=>3]
//        );
    }
}

<?php


namespace App\Helper\Payment;


use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Http;
use PayPal\Api\VerifyWebhookSignature;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersAuthorizeRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Payments\AuthorizationsVoidRequest;

class PayPalHelper
{
    /**
     * Set up and return PayPal PHP SDK environment with PayPal access credentials.
     * This sample uses SandboxEnvironment. In production, use LiveEnvironment.
     */
    private static function clientId()
    {
        return getenv("CLIENT_ID") ?: "PAYPAL-SANDBOX-CLIENT-ID";
    }

    private static function clientSecret()
    {
        return getenv("CLIENT_SECRET") ?: "PAYPAL-SANDBOX-CLIENT-SECRET";
    }

    private static function environment()
    {
        //if else for production
        return new SandboxEnvironment(self::clientId(), self::clientSecret());
    }

    public static function client()
    {
        return new PayPalHttpClient(self::environment());
    }

    public static function accessToken()
    {

//        $sdkConfig = array(
//            "mode" => "sandbox"
//        );
//
//        $request = new OAuthTokenCredential(self::clientId(), self::clientSecret());
//        return $request->getAccessToken($sdkConfig);

        $url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token/';
        if (env('APP_ENV') == 'local') {
            $url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
        ])->withBasicAuth(self::clientId(), self::clientSecret())->asForm()->post($url, [
            'grant_type' => 'client_credentials'
        ]);
        $res = $response->json();
        return $res['access_token'];

    }

    public static function createOrder($price)
    {
        $client = self::client();
//        'processing_instruction'=>'ORDER_SAVED_EXPLICITLY',
//             'processing_instruction'=>'NO_INSTRUCTION',
        $request = new OrdersCreateRequest();
        $request->body = array(
            'intent' => 'CAPTURE',
            'application_context' =>
                [
                    'return_url' => 'https://example.com/return',
                    'cancel_url' => 'https://example.com/cancel',
                    'shipping_preference' => 'NO_SHIPPING'
                ],
            'purchase_units' =>
                [
                    [
                        'amount' =>
                            [
                                'currency_code' => 'EUR',
                                'value' => $price
                            ]
                    ]
                ]
        );

        return $client->execute($request);
    }

    public static function saveOrder($orderId)
    {
        $url = 'https://api-m.sandbox.paypal.com/v2/checkout/orders/' . $orderId . '/save';
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
        ])->withBasicAuth(self::clientId(), self::clientSecret())->post($url, []);
        $res = $response->json();
        return $res;
    }

    public static function voidOrder($orderId)
    {
        $url = 'https://api-m.sandbox.paypal.com/v2/checkout/orders/' . $orderId . '/void';
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
        ])->withBasicAuth(self::clientId(), self::clientSecret())->post($url, []);
        $res = $response->json();
        return $res;
    }


    public static function captureOrder($orderId)
    {
        $request = new OrdersCaptureRequest($orderId);

        $client = self::client();
        $response = $client->execute($request);
        return $response;
    }

    public static function getOrder($orderId)
    {
        $request = new OrdersGetRequest($orderId);

        $client = self::client();
        $response = $client->execute($request);
        return $response;
    }

    public static function cancelOrder($id)
    {
        $token = self::accessToken();
        $url = 'https://api-m.paypal.com/v1/checkout/orders/' . $id;
        if (env('APP_ENV') == 'local') {
            $url = 'https://api-m.sandbox.paypal.com/v1/checkout/orders/' . $id;
        }
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Accept-Language' => 'en_US',
        ])->withToken($token)->delete($url);
        return $response->json();

    }

    public static function authorizeOrder($orderId)
    {
        $request = new OrdersAuthorizeRequest($orderId);
        $request->body = '{}';
        // 3. Call PayPal to authorize an order
        $client = self::client();
        return $client->execute($request);
    }

    public static function voidPaymentAuthorization($orderId)
    {
        $client = self::client();
        $request = new AuthorizationsVoidRequest($orderId);
        return $client->execute($request);
    }


    public static function verifyWebHook($webhookId = '')
    {
        $requestBody = file_get_contents('php://input');
        $apiContext = new ApiContext(new OAuthTokenCredential(self::clientId(), self::clientSecret()));

        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_UPPER);
        if (
            (!array_key_exists('PAYPAL-AUTH-ALGO', $headers)) ||
            (!array_key_exists('PAYPAL-TRANSMISSION-ID', $headers)) ||
            (!array_key_exists('PAYPAL-CERT-URL', $headers)) ||
            (!array_key_exists('PAYPAL-TRANSMISSION-SIG', $headers)) ||
            (!array_key_exists('PAYPAL-TRANSMISSION-TIME', $headers))
        ) {
            throw new \Exception(500);
        }

        $webhookID = $webhookId;

        $signatureVerification = new VerifyWebhookSignature();
        $signatureVerification->setAuthAlgo($headers['PAYPAL-AUTH-ALGO']);
        $signatureVerification->setTransmissionId($headers['PAYPAL-TRANSMISSION-ID']);
        $signatureVerification->setCertUrl($headers['PAYPAL-CERT-URL']);
        $signatureVerification->setWebhookId($webhookID);
        $signatureVerification->setTransmissionSig($headers['PAYPAL-TRANSMISSION-SIG']);
        $signatureVerification->setTransmissionTime($headers['PAYPAL-TRANSMISSION-TIME']);

        $signatureVerification->setRequestBody($requestBody);
        $request = clone $signatureVerification;

        try {

            $output = $signatureVerification->post($apiContext);

        } catch (\Exception $ex) {
            throw new \Exception(500);
        }
        $sigVerificationResult = $output->getVerificationStatus();
        if ($sigVerificationResult != "SUCCESS") {

            throw new \Exception(500);
        } else if ($sigVerificationResult == "SUCCESS") {
            $requestBodyDecode = json_decode($requestBody);
            $paymentSystemID = $requestBodyDecode->id;
            $eventType = $requestBodyDecode->event_type;
        }

    }
}

<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PaymobPaymentService extends BasePaymentService implements PaymentGatewayInterface
{
    protected $api_key;
    protected $integrations_id;

    public function __construct()
    {
        $this->base_url = env("PAYMOB_BASE_URL");
        $this->api_key = env("PAYMOB_API_KEY");
        $this->header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $this->integrations_id = [4952690, 4948887];
    }

    protected function generateToken()
    {
        $response = $this->buildRequest('POST', '/api/auth/tokens', [
            'api_key' => $this->api_key
        ]);

        $data = $response->getData(true);

        if (isset($data['token'])) {
            return $data['token'];
        }

        Log::error('Failed to generate Paymob token', ['response' => $data]);

        return null;
    }

    public function sendPayment($user, Request $request): array
    {
        $order = $user->orders()->latest()->first();
        $token = $this->generateToken();

        if (!$token) {
            return ['success' => false, 'url' => route('payment.failed')];
        }

        $this->header['Authorization'] = 'Bearer ' . $token;

        $data = $request->all();
        $data['api_source'] = "INVOICE";
        $data['integrations'] = $this->integrations_id;
        $data['amount_cents'] = ($order->final_price * 100);
        $data['shipping_data']['first_name'] = $user->name;
        $data['shipping_data']['email'] = $user->email;

        $response = $this->buildRequest('POST', '/api/ecommerce/orders', $data);
        $responseData = $response->getData(true);

        if (
            isset($responseData['success']) &&
            $responseData['success'] &&
            isset($responseData['data']['url'])
        ) {
            return ['success' => true, 'url' => $responseData['data']['url']];
        }

        Log::error('Paymob response error', ['response' => $responseData]);

        return ['success' => false, 'url' => route('payment.failed')];
    }

    public function callBack(Request $request): bool
    {
        $response = $request->all();
        Storage::put('paymob_response.json', json_encode($response));

        if (isset($response['success']) && $response['success'] === 'true') {
            return true;
        }

        return false;
    }
}

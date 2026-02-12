<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Services\Payments\DS_PaymentFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class DS_PaymentWebhookController extends Controller
{
    public function handle(Request $request, string $gateway): Response
    {
        try {
            Log::info("Webhook received for gateway: {$gateway}");

            $provider = DS_PaymentFactory::make($gateway);
            $success = $provider->handleWebhook($request);

            if ($success) {
                return response('Webhook Handled', 200);
            }

            return response('Webhook Handling Failed', 400);

        } catch (\Exception $e) {
            Log::error("Webhook Error ({$gateway}): " . $e->getMessage());
            return response('Webhook Error', 500);
        }
    }
}

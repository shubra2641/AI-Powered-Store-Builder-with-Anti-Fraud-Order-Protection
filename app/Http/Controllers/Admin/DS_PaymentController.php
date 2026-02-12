<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DS_PaymentGateway;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\DS_StorePaymentRequest;
use App\Http\Requests\Admin\DS_UpdatePaymentRequest;
use App\Services\Payments\DS_PaymentGatewayService;

class DS_PaymentController extends Controller
{
    use DS_TranslationHelper;

    public function __construct(
        protected DS_PaymentGatewayService $gatewayService
    ) {}

    public function index(): View
    {
        $gateways = DS_PaymentGateway::all();
        return view('admin.payments.index', compact('gateways'));
    }

    public function store(DS_StorePaymentRequest $request): RedirectResponse
    {
        $this->gatewayService->createGateway($request->validated());

        $this->notifySuccess('admin.payment_gateway_created_success');

        return back();
    }

    public function edit(DS_PaymentGateway $gateway): View
    {
        return view('admin.payments.edit', compact('gateway'));
    }

    public function getData(DS_PaymentGateway $gateway): JsonResponse
    {
        return response()->json($gateway);
    }

    public function update(DS_UpdatePaymentRequest $request, DS_PaymentGateway $gateway): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['environment'])) {
            $data['mode'] = $data['environment'] === 'sandbox_test' ? 'sandbox' : 'live';
            $data['is_test_mode'] = $data['environment'] === 'sandbox_test' ? 1 : 0;
            unset($data['environment']);
        }

        $gateway->update($data);

        $this->notifySuccess('admin.payment_gateway_updated_success');

        return back();
    }

    public function destroy(DS_PaymentGateway $gateway): RedirectResponse
    {
        $gateway->delete();
        $this->notifySuccess('admin.payment_gateway_deleted_success');
        return back();
    }

    public function toggleStatus(DS_PaymentGateway $gateway): RedirectResponse
    {
        $gateway->update(['is_active' => !$gateway->is_active]);
        
        $this->notifySuccess('admin.status_updated_success');
        
        return back();
    }
}

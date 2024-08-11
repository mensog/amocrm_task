<?php

namespace App\Http\Controllers;

use AmoCRM\Exceptions\AmoCRMApiException;
use App\Http\Requests\PushLeadRequest;
use App\Services\LeadService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct(
        private LeadService $service,
    ) {
    }

    public function create(Request $request): View
    {
        return view('form');
    }

    public function store(PushLeadRequest $request)
    {
        try {
            $this->service->submitLeadForm($request->validated());
            return redirect()->back()->with('message', __('Сделка создана!'));
        } catch (AmoCRMApiException | Exception $e) {
            // $request->session()->put('lead_data', $request->validated());
            return redirect()->route('amocrm.redirectoauth');
        }
    }
}

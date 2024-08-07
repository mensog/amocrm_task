<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Exceptions\AmoCRMApiException;
use App\Adapters\AmoCrmClient;
use App\Crm\AmoCrm;
use App\Http\Requests\PushLeadRequest;
use App\Services\AmoCrmOAuthService;

class AmoCRMController extends Controller
{

    public function __construct(
        private AmoCrmClient $amoCrmClient,
        private AmoCrmOAuthService $oauthService,
    ) {
    }

    public function showForm()
    {
        return view('amocrm_form');
    }

    public function auth()
    {
        $state = bin2hex(random_bytes(16));
        session(['oauth2state' => $state]);

        $authorizationUrl = $this->oauthService->getAuthorizationUrl($state);

        return redirect()->away($authorizationUrl);
    }

    public function handleAmoCRMCb(Request $request)
    {
        $code = $request->get('code');
        $state = $request->get('state');
        dump($request);
        if (!$code || !$state || $state !== session('oauth2state')) {
            Log::error('Invalid authorization state or missing authorization code', [
                'code' => $code,
                'state' => $state,
                'session_state' => session('oauth2state')
            ]);
            abort(400, 'Invalid authorization state or missing authorization code');
        }

        try {
            $accessToken = $this->oauthService->handleCallback($code);
            $this->oauthService->saveToken($accessToken);
            return redirect()->route('amocrm.form')->with('success', 'Authorization successful');
        } catch (AmoCRMoAuthApiException $e) {
            Log::error('Authorization error: ' . $e->getMessage(), [
                'code' => $code,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Authorization error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage(), [
                'code' => $code,
                'exception' => $e
            ]);
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }

    public function submitForm(PushLeadRequest $request)
    {
        $validated = $request->validated();
        $accessToken = Cache::get('amocrm_access_token');

        dump($accessToken);
        if (!$accessToken) {
            return redirect()->route('amocrm.authorize');
        }

        $this->amoCrmClient->setAccessToken($this->oauthService->getAccessToken());
        $this->amoCrmClient->setAccountBaseDomain(config('crm.' . AmoCrm::getKey() . '.domain'));

        try {
            $this->amoCrmClient->pushLead($validated);

            return redirect()->back()->with('success', 'Заявка успешно отправлена в AmoCRM');
        } catch (AmoCRMApiException $e) {
            Log::error('AmoCRM API error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $validated
            ]);
            return response()->json(['error' => 'AmoCRM API error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $validated
            ]);
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }

    public function leadAdd(Request $request)
    {
        Log::info($request);
    }

    public function contactAdd(Request $request)
    {
        Log::info($request);
    }
}

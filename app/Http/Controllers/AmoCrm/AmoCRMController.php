<?php

namespace App\Http\Controllers\AmoCrm;

use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AmoCrm\AuthorizationRequest;
use App\Services\AmoCrm\OAuthService;
use Illuminate\Http\Request;

class AmoCRMController extends Controller
{
    public function __construct(
        private OAuthService $oauthService,
    ) {
    }

    public function redirectToOAuth()
    {
        $authUrl = $this->oauthService->getAuthorizationUrl();
        return redirect()->away($authUrl);
    }

    public function authorizationOAuth(AuthorizationRequest $request)
    {
        try {
            $this->oauthService->processToken($request->validated());
            return redirect()->route('lead.create')->with('message', 'Authorization successful');
        } catch (AmoCRMoAuthApiException $e) {
            return redirect()->route('lead.create')->withErrors($e->getTitle());
        } catch (\Exception $e) {
            return redirect()->route('lead.create')->withErrors($e->getMessage());
        }
    }
}

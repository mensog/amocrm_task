<?php

namespace App\Providers;

use App\Adapters\AmoCrmClient;
use App\Crm\AmoCrm;
use App\Services\AmoCrm\OAuthService;
use Illuminate\Support\ServiceProvider;

class CrmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AmoCrmClient::class, function () {
            $amoClient = (new AmoCrmClient(
                AmoCrm::getConfig('client_id'),
                AmoCrm::getConfig('client_secret'),
                AmoCrm::getConfig('redirect_uri')
            ));

            if (OAuthService::isValidToken()) {
                $amoClient->setAccountBaseDomain(AmoCrm::getConfig('domain'));
                $amoClient->setAccessToken(OAuthService::getAccessToken());
            }

            return $amoClient;
        });
    }

    public function boot(): void
    {
        //
    }
}

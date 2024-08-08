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
                config('crm.' . AmoCrm::getKey() . '.client_id'),
                config('crm.' . AmoCrm::getKey() . '.client_secret'),
                config('crm.' . AmoCrm::getKey() . '.redirect_uri')
            ))->setAccountBaseDomain(config('crm.' . AmoCrm::getKey() . '.domain'));

            if (OAuthService::isValidToken()) {
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

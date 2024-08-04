<?php

namespace App\Providers;

use App\Adapters\AmoCrmClient;
use App\Crm\AmoCrm;
use Illuminate\Support\ServiceProvider;

class CrmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AmoCrmClient::class, fn () => new AmoCrmClient(
            config(AmoCrm::getKey() . 'amocrm_client_id'),
            config(AmoCrm::getKey() . 'amocrm_client_secret'),
            config(AmoCrm::getKey() . 'amocrm_redirect_uri')
        ));
    }

    public function boot(): void
    {
        //
    }
}

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
            config('crm.' . AmoCrm::getKey() . '.client_id'),
            config('crm.' . AmoCrm::getKey() . '.client_secret'),
            config('crm.' . AmoCrm::getKey() . '.redirect_uri')
        ));
    }

    public function boot(): void
    {
        //
    }
}

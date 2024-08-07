<?php

namespace App\Crm;

use App\Adapters\AmoCrmClient;
use App\Interfaces\CrmPushClientInterface;
use App\Interfaces\CrmInterface;

class AmoCrm implements CrmInterface
{
    public static function getKey(): string
    {
        return 'amocrm';
    }

    public static function getClient(): CrmPushClientInterface
    {
        return app(AmoCrmClient::class);
    }
}

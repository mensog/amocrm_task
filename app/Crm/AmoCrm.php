<?php

namespace App\Crm;

use App\Adapters\AmoCrmClient;
use App\Exceptions\AmoCrm\TokenNotValidException;
use App\Interfaces\CrmPushClientInterface;
use App\Interfaces\CrmInterface;
use App\Services\AmoCrm\OAuthService;

class AmoCrm implements CrmInterface
{
    public static function getKey(): string
    {
        return 'amocrm';
    }

    public static function getClient(): CrmPushClientInterface
    {
        if (!OAuthService::isValidToken()) {
            throw new TokenNotValidException;
        }

        return app(AmoCrmClient::class);
    }
}

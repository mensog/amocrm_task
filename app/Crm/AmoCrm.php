<?php

namespace App\Crm;

use App\Interfacas\CrmPushClientInterface;
use App\Interfacas\CrmPushInterface;
use App\Interface\CrmInterface;

class AmoCrm implements CrmInterface
{
    public static function getKey(): string
    {
        return 'amocrm';
    }

    public static function getClient(): CrmPushClientInterface
    {
        
    }
}

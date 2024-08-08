<?php

namespace App\Crm\Factory;

use App\Crm\AmoCrm;
use App\Interfaces\CrmInterface;

class CrmFactory
{
    public static function make(string $crm): CrmInterface
    {
        return match ($crm) {
            AmoCrm::getKey() => app(AmoCrm::class),
            // OtherCrm::getKey() => app(OtherCrm::class),
        };
    }
}

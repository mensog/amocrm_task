<?php

namespace App\Interfaces;

use App\Adapters\AmoCrmClient;

interface CrmInterface
{
    public static function getKey(): string;
    /**
     * @return \App\Interfaces\CrmPushClientInterface|AmoCrmClient
     */
    public static function getClient(): CrmPushClientInterface;
}
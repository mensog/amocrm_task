<?php

namespace App\Interfaces;

use App\Adapters\AmoCrmClient;
use Illuminate\Support\Facades\Config;

interface CrmInterface
{
    public static function getKey(): string;
    /**
     * @return \App\Interfaces\CrmPushClientInterface|AmoCrmClient
     */
    public static function getClient(): CrmPushClientInterface;

    public static function getConfig(string $key): string;
}
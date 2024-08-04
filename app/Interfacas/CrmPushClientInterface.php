<?php

namespace App\Interfacas;

interface CrmPushClientInterface
{
    public function pushLead(array $lead): bool;
}

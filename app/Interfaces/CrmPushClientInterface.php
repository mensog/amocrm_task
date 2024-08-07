<?php

namespace App\Interfaces;

interface CrmPushClientInterface
{
    public function pushLead(array $lead): bool;
}

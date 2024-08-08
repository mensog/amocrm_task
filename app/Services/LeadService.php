<?php

namespace App\Services;

use App\Crm\Factory\CrmFactory;

class LeadService
{
    public function submitLeadForm(array $validated)
    {
        $crm = CrmFactory::make($validated['crm']);

        return $crm->getClient()->pushLead($validated);
    }
}

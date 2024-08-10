<?php

namespace App\Adapters;

use AmoCRM\Client\AmoCRMApiClient;
use App\Crm\Traits\AmoCrm\InteractsWithLeads;
use App\Interfaces\CrmPushClientInterface;

class AmoCrmClient extends AmoCRMApiClient implements CrmPushClientInterface
{
    use InteractsWithLeads;

    public function pushLead(array $lead): bool
    {
        $contact = $this->makeContact($lead);
        $lead    = $this->makeLead($lead, $contact);

        $this->contacts()->addOne($contact);
        $this->leads()->addOne($lead);

        return true;
    }

}

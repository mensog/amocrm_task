<?php

namespace App\Adapters;

use AmoCRM\Client\AmoCRMApiClient;
use App\Crm\Traits\AmoCrm\InteractsWithLeads;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use App\Builders\AmoCrm\ContactBuilder;
use App\Builders\AmoCrm\LeadBuilder;
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

    protected function makeLead(array $validated, ContactModel $contact): LeadModel
    {
        return (new LeadBuilder)
            ->setName($validated['name'])
            ->setPrice($validated['price'])
            ->setTimeSpent($validated['time_spent'])
            ->addContact($contact)
            ->build();
    }

    protected function makeContact(array $validated): ContactModel
    {
        return (new ContactBuilder)
            ->setName($validated['name'])
            ->setEmail($validated['email'])
            ->setPhone($validated['phone'])
            ->build();
    }
}

<?php

namespace App\Crm\Traits\AmoCrm;

use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use App\Builders\AmoCrm\ContactBuilder;
use App\Builders\AmoCrm\LeadBuilder;

trait InteractsWithLeads
{
    protected function makeLead(array $validated, ContactModel $contact): LeadModel
    {
        return (new LeadBuilder())
            ->setName($validated['name'])
            ->setPrice($validated['price'])
            ->setTimeSpent($validated['time_spent'])
            ->addContact($contact)
            ->build();
    }

    protected function makeContact(array $validated): ContactModel
    {
        return (new ContactBuilder())
            ->setName($validated['name'])
            ->setEmail($validated['email'])
            ->setPhone($validated['phone'])
            ->build();
    }
}
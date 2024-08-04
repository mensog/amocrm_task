<?php

namespace App\Adapters;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\Builders\AmoCrm\ContactBuilder;
use App\Interfacas\CrmPushClientInterface;

class AmoCrmClient extends AmoCRMApiClient implements CrmPushClientInterface
{
    public function pushLead(array $lead): bool
    {
        $validated = $lead;

        $contact = $this->makeContact($validated);

        $lead = new LeadModel();
        $lead->setName('New Lead')
            ->setPrice($validated['price'])
            ->setCustomFieldsValues(
                (new CustomFieldsValuesCollection())
                    ->add(
                        (new CheckboxCustomFieldValuesModel())
                            ->setFieldId((int)env('AMOCRM_TIME_SPENT_FIELD_ID'))
                            ->setValues(
                                (new CheckboxCustomFieldValueCollection())
                                    ->add((new CheckboxCustomFieldValueModel())->setValue($validated['time_spent']))
                            )
                    )
            )
            ->setContacts(
                (new ContactsCollection())
                    ->add($contact)
            );
        $lead = $this->leads()->addOne($lead);

        return true;
    }

    public function makeContact(array $validated): ContactModel
    {
        $contact = (new ContactBuilder)
            ->setName($validated['name'])
            ->setEmail($validated['email'])
            ->setPhone($validated['phone'])
            ->build();

        return $this->contacts()->addOne($contact);
    }
}

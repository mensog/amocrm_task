<?php

namespace App\Builders\AmoCrm;

use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\Crm\AmoCrm;
use App\Interfaces\BuilderInterface;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Models\ContactModel;

class LeadBuilder implements BuilderInterface
{
    private CustomFieldsValuesCollection $fields;
    private ContactsCollection $contacts;

    public function __construct(
        private LeadModel $leadModel = new LeadModel
    ) {
        $this->fields = new CustomFieldsValuesCollection();
        $this->contacts = new ContactsCollection();
    }

    public function setName(string $name): static
    {
        $this->leadModel->setName($name);
        return $this;
    }

    public function setPrice(int $price): static
    {
        $this->leadModel->setPrice($price);
        return $this;
    }

    public function setTimeSpent(int $time_spent): static
    {
        $this->fields->add(
            (new CheckboxCustomFieldValuesModel())
                ->setFieldId(config('crm.' . AmoCrm::getKey() . '.time_spent_field_id'))
                ->setValues(
                    (new CheckboxCustomFieldValueCollection())
                        ->add((new CheckboxCustomFieldValueModel())->setValue($time_spent))
                ));
        $this->leadModel->setCustomFieldsValues($this->fields);

        return $this;
    }    

    public function addContact(ContactModel $contactModel): static
    {
        $this->contacts->add($contactModel);
        $this->leadModel->setContacts($this->contacts);

        return $this;
    }

    public function build(): LeadModel
    {
        return $this->leadModel;
    }
}

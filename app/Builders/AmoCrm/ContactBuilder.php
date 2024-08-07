<?php

namespace App\Builders\AmoCrm;

use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use App\Interfaces\BuilderInterface;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;

class ContactBuilder implements BuilderInterface
{
    private CustomFieldsValuesCollection $fields;

    public function __construct(
        private ContactModel $contactModel = new ContactModel
    ) {
        $this->fields = new CustomFieldsValuesCollection();
    }

    public function setName(string $name): static
    {
        $this->contactModel->setName($name);
        return $this;
    }

    public function setEmail(string $email): static
    {
        $this->fields->add((new MultitextCustomFieldValuesModel)
            ->setFieldCode('EMAIL')
            ->setValues(
                (new MultitextCustomFieldValueCollection)
                    ->add((new MultitextCustomFieldValueModel)->setValue($email))
            ));

        $this->contactModel->setCustomFieldsValues($this->fields);

        return $this;
    }

    public function setPhone(string $phone): static
    {
        $this->fields->add((new MultitextCustomFieldValuesModel)
            ->setFieldCode('PHONE')
            ->setValues(
                (new MultitextCustomFieldValueCollection)
                    ->add((new MultitextCustomFieldValueModel)->setValue($phone))
            ));
        $this->contactModel->setCustomFieldsValues($this->fields);

        return $this;
    }

    public function build(): ContactModel
    {
        return $this->contactModel;
    }
}

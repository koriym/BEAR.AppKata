<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form\Admin;

use Aura\Input\Builder;
use MyVendor\MyProject\Form\Admin\Fieldset\AddressFieldset;
use MyVendor\MyProject\Form\Admin\Fieldset\AddressFilter;
use MyVendor\MyProject\Form\AntiCsrfSetter;
use MyVendor\MyProject\Form\ExtendedForm;
use stdClass;

/** @psalm-suppress PropertyNotSetInConstructor */
class AdminFieldsetDemoForm extends ExtendedForm
{
    use AntiCsrfSetter;

    public function init(): void
    {
        $this->builder = new Builder([
            'address' => static function () {
                return new AddressFieldset(
                    new Builder(),
                    new AddressFilter(),
                );
            },
        ]);

        $this->setFieldset('home', 'address');
        /** @psalm-suppress UndefinedMethod */
        /** @psalm-suppress TooManyArguments */
        $this->filter
            ->validate('home')
            ->is('callback', static function (stdClass $subject, string $field) {
                /** @var array{zip?: string|null, state?: string|null, city?: string|null, street?: string|null, houseType?: string|null, smartphones?: array<string>|null} $home */
                $home = $subject->$field;
                if (! isset($home['zip'], $home['state'], $home['city'], $home['street'], $home['houseType'], $home['smartphones'])) {
                    return false;
                }

                $filter = new AddressFilter();
                $values = (object) $home;

                return $filter->values($values);
            });

        $this->setCollection('deliveries', 'address');
        /** @psalm-suppress UndefinedMethod */
        /** @psalm-suppress TooManyArguments */
        $this->filter
            ->validate('deliveries')
            ->is('callback', static function (stdClass $subject, string $field) {
                $isValid = true;

                /** @var array<int, array{zip?: string|null, state?: string|null, city?: string|null, street?: string|null, houseType?: string|null, smartphones?: array<string>|null}> $deliveries */
                $deliveries = $subject->$field;
                foreach ($deliveries as $delivery) {
                    if (! isset($delivery['zip'], $delivery['state'], $delivery['city'], $delivery['street'], $delivery['houseType'], $delivery['smartphones'])) {
                        $isValid = false;
                        continue;
                    }

                    $filter = new AddressFilter();
                    $values = (object) $delivery;
                    if ($filter->values($values)) {
                        continue;
                    }

                    $isValid = false;
                }

                return $isValid;
            });

        $this->setField('note', 'textarea');

        $this->setField('agree', 'checkbox')
             ->setAttribs([
                 'value' => 'yes',
                 'label' => 'YES',
                 'value_unchecked' => 'no',
             ]);

        /** @psalm-suppress UndefinedMethod */
        $this->setField('submit', 'submit');
    }
}

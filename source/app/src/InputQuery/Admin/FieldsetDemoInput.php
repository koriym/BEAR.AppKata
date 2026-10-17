<?php

declare(strict_types=1);

namespace MyVendor\MyProject\InputQuery\Admin;

use MyVendor\MyProject\InputQuery\Admin\Fieldset\Address;
use Ray\InputQuery\Attribute\Input;

readonly class FieldsetDemoInput
{
    /**
     * @param list<Address>|null $deliveries
     *
     * @psalm-suppress UndefinedAttributeClass
     */
    public function __construct(
        #[Input]
        public string $note,
        #[Input]
        public string $agree,
        #[Input]
        public string|null $submit,
        #[Input]
        public Address|null $home = null,
        #[Input(item: Address::class)]
        public array|null $deliveries = [],
    ) {
    }
}

<?php

declare(strict_types=1);

namespace MyVendor\MyProject\InputQuery\Admin;

use Ray\InputQuery\Attribute\Input;

readonly class DeleteInput
{
    /** @psalm-suppress UndefinedAttributeClass */
    public function __construct(
        #[Input]
        public string|null $continue,
    ) {
    }
}

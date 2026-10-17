<?php

declare(strict_types=1);

namespace MyVendor\MyProject\InputQuery\Admin;

use Ray\InputQuery\Attribute\Input;
use SensitiveParameter;

readonly class ResetPasswordInput
{
    /** @psalm-suppress UndefinedAttributeClass */
    public function __construct(
        #[Input]
        #[SensitiveParameter]
        public string $password,
        #[Input]
        public string $signature,
        #[Input]
        public string|null $continue,
    ) {
    }
}

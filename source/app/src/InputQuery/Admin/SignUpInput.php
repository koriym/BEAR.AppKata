<?php

declare(strict_types=1);

namespace MyVendor\MyProject\InputQuery\Admin;

use Ray\InputQuery\Attribute\Input;
use SensitiveParameter;

readonly class SignUpInput
{
    /** @psalm-suppress UndefinedAttributeClass */
    public function __construct(
        #[Input]
        public string $username,
        #[Input]
        public string $displayName,
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

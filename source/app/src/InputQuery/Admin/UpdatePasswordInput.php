<?php

declare(strict_types=1);

namespace MyVendor\MyProject\InputQuery\Admin;

use Ray\InputQuery\Attribute\Input;
use SensitiveParameter;

readonly class UpdatePasswordInput
{
    /** @psalm-suppress UndefinedAttributeClass */
    public function __construct(
        #[Input]
        #[SensitiveParameter]
        public string $oldPassword,
        #[Input]
        #[SensitiveParameter]
        public string $password,
        #[Input]
        #[SensitiveParameter]
        public string $passwordConfirmation,
        #[Input]
        public string|null $update,
    ) {
    }
}

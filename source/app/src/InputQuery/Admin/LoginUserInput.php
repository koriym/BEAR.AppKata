<?php

declare(strict_types=1);

namespace MyVendor\MyProject\InputQuery\Admin;

use Ray\InputQuery\Attribute\Input;
use SensitiveParameter;

final readonly class LoginUserInput
{
    /** @psalm-suppress UndefinedAttributeClass */
    public function __construct(
        #[Input]
        public string $username,
        #[Input]
        #[SensitiveParameter]
        public string $password,
        #[Input]
        public string $remember,
        #[Input]
        public string|null $login,
    ) {
    }

    public function isValid(): bool
    {
        return $this->username !== '' && $this->password !== '';
    }

//    public function isSubmitted(): bool
//    {
//        return $this->login !== '';
//    }
}

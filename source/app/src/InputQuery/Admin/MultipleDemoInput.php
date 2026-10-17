<?php

declare(strict_types=1);

namespace MyVendor\MyProject\InputQuery\Admin;

use Ray\InputQuery\Attribute\Input;

readonly class MultipleDemoInput
{
    /**
     * @param list<string> $fruits
     * @param list<string> $languages
     * @param list<string> $programmingLanguages
     *
     * @psalm-suppress UndefinedAttributeClass
     */
    public function __construct(
        #[Input]
        public string $primaryLanguage,
        #[Input]
        public string|null $submit,
        #[Input]
        public array $fruits = [],
        #[Input]
        public array $languages = [],
        #[Input]
        public array $programmingLanguages = [],
    ) {
    }
}

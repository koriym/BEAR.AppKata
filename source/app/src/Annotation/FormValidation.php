<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Annotation;

use Attribute;
use Ray\WebFormModule\Annotation\AbstractValidation;

#[Attribute(Attribute::TARGET_METHOD)]
final class FormValidation extends AbstractValidation
{
    /** @SuppressWarnings("PHPMD.BooleanArgumentFlag") */
    public function __construct(string $form = 'form', public bool $antiCsrf = false, public string|null $onFailure = null)
    {
        $this->form = $form;
    }
}

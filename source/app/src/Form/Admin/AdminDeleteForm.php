<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form\Admin;

use MyVendor\MyProject\Form\AntiCsrfSetter;
use MyVendor\MyProject\Form\ExtendedForm;

/** @psalm-suppress PropertyNotSetInConstructor */
class AdminDeleteForm extends ExtendedForm
{
    use AntiCsrfSetter;

    public function init(): void
    {
        /** @psalm-suppress UndefinedMethod */
        $this->setField('continue', 'submit');
    }
}

<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form\Admin;

use MyVendor\MyProject\Form\AntiCsrfSetter;
use MyVendor\MyProject\Form\ExtendedForm;

/**
 * @property string $mode
 * @psalm-suppress PropertyNotSetInConstructor
 */
class AdminContactDemoForm extends ExtendedForm
{
    use AntiCsrfSetter;

    public function init(): void
    {
        /** @psalm-suppress UndefinedMethod */
        $this->setField('username', 'text')
             ->setAttribs([
                 'autofocus' => '',
                 'autocomplete' => 'email',
                 'placeholder' => 'username',
                 'required' => 'required',
             ]);
        $this->filter->validate('username')->is('alnum');
        $this->filter->useFieldMessage('username', 'Name must be alphabetic only.');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('mode', 'submit');
    }
}

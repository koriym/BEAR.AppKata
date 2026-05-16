<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form\Admin;

use MyVendor\MyProject\Form\AntiCsrfSetter;
use MyVendor\MyProject\Form\ExtendedForm;

/** @psalm-suppress PropertyNotSetInConstructor */
class AdminPasswordConfirmForm extends ExtendedForm
{
    use AntiCsrfSetter;

    public function init(): void
    {
        /** @psalm-suppress UndefinedMethod */
        $this->setField('password', 'password')
             ->setAttribs([
                 'autofocus' => '',
                 'autocomplete' => 'current-password',
                 'placeholder' => '',
                 'required' => 'required',
                 'title' => '有効なパスワードを入力してください',
             ]);
        $this->filter->validate('password')->is('string');
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('password')->is('regex', '/^[A-Za-z0-9!@#$%^&*]+$/i');
        $this->filter->useFieldMessage('password', '有効なパスワードを入力してください');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('continue', 'submit');
    }
}

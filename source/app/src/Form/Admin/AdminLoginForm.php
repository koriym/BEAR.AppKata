<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form\Admin;

use MyVendor\MyProject\Form\AntiCsrfSetter;
use MyVendor\MyProject\Form\ExtendedForm;

/** @psalm-suppress PropertyNotSetInConstructor */
class AdminLoginForm extends ExtendedForm
{
    use AntiCsrfSetter;

    public function init(): void
    {
        /** @psalm-suppress UndefinedMethod */
        $this->setField('username', 'text')
             ->setAttribs([
                 'autofocus' => '',
                 'autocomplete' => 'username',
                 'placeholder' => '',
                 'required' => 'required',
                 'title' => '有効なユーザー名を入力してください',
             ]);
        $this->filter->validate('username')->is('string');
        $this->filter->useFieldMessage('username', '有効なユーザー名を入力してください');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('password', 'password')
             ->setAttribs([
                 'autocomplete' => 'current-password',
                 'placeholder' => '',
                 'required' => 'required',
                 'title' => '有効なパスワードを入力してください',
             ]);
        $this->filter->validate('password')->is('string');
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('password')->is('regex', '/^[A-Za-z0-9!@#$%^&*]+$/i');
        $this->filter->useFieldMessage('password', '有効なパスワードを入力してください');

        $this->setField('remember', 'checkbox')
             ->setAttribs([
                 'value' => 'yes',
                 'label' => 'YES',
                 'value_unchecked' => 'no',
             ]);

        /** @psalm-suppress UndefinedMethod */
        $this->setField('login', 'submit');
    }
}

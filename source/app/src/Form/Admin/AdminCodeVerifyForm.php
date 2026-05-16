<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form\Admin;

use MyVendor\MyProject\Form\AntiCsrfSetter;
use MyVendor\MyProject\Form\ExtendedForm;

/** @psalm-suppress PropertyNotSetInConstructor */
class AdminCodeVerifyForm extends ExtendedForm
{
    use AntiCsrfSetter;

    public function init(): void
    {
        $this->setField('uuid', 'hidden');
        $this->filter->validate('uuid')->is('string');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('code', 'text')
             ->setAttribs([
                 'autofocus' => '',
                 'autocomplete' => 'one-time-code',
                 'placeholder' => '',
                 'required' => 'required',
                 'pattern' => '^\d+$',
                 'title' => 'メールに記載されたコードを入力してください',
             ]);
        $this->filter->validate('code')->is('int');
        $this->filter->useFieldMessage('code', 'メールに記載されたコードを入力してください');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('continue', 'submit');
    }
}

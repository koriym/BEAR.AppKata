<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form\Admin;

use AppCore\Infrastructure\Query\BadPasswordQueryInterface;
use MyVendor\MyProject\Form\AntiCsrfSetter;
use MyVendor\MyProject\Form\ExtendedForm;
use Ray\Di\Di\Inject;
use stdClass;

/** @psalm-suppress PropertyNotSetInConstructor */
class AdminPasswordResetForm extends ExtendedForm
{
    use AntiCsrfSetter;

    private BadPasswordQueryInterface $badPasswordQuery;

    #[Inject]
    public function setBadPasswordQuery(BadPasswordQueryInterface $badPasswordQuery): void
    {
        $this->badPasswordQuery = $badPasswordQuery;
    }

    public function init(): void
    {
        /** @psalm-suppress UndefinedMethod */
        $this->setField('password', 'password')
             ->setAttribs([
                 'autocomplete' => 'current-password',
                 'placeholder' => '',
                 'required' => 'required',
                 'minlength' => 8,
                 'maxlength' => 20,
                 'pattern' => '^[\x20-\x7E]+$', // ASCII 文字列
                 'title' => '新しいパスワードは8文字以上20文字以下の英数字記号(!@#$%^&*)で入力してください',
             ]);
        $this->filter->validate('password')->is('string');
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('password')->is('regex', '/^[A-Za-z0-9!@#$%^&*]+$/i');
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('password')->is('strlenBetween', 8, 20);
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('password')
            ->is('callback', function (stdClass $subject, string $field) {
                return $this->badPasswordQuery->item($subject->$field) === null;
            });
        $this->filter->useFieldMessage('password', '新しいパスワードは8文字以上20文字以下の英数字記号(!@#$%^&*)で入力してください');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('signature', 'hidden');
        $this->filter->validate('signature')->isNotBlank()->is('string');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('continue', 'submit');
    }
}

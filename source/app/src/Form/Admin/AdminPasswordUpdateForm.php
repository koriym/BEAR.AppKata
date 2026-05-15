<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form\Admin;

use AppCore\Infrastructure\Query\BadPasswordQueryInterface;
use MyVendor\MyProject\Form\AntiCsrfSetter;
use MyVendor\MyProject\Form\ExtendedForm;
use Ray\Di\Di\Inject;
use stdClass;

/** @psalm-suppress PropertyNotSetInConstructor */
class AdminPasswordUpdateForm extends ExtendedForm
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
        $this->setField('oldPassword', 'password')
             ->setAttribs([
                 'autofocus' => '',
                 'autocomplete' => 'current-password',
                 'id' => 'current-password',
                 'placeholder' => '',
                 'required' => 'required',
                 'title' => '現在のパスワードを入力してください',
             ]);
        $this->filter->validate('oldPassword')->isNotBlank();
        $this->filter->validate('oldPassword')->is('string');
        $this->filter->useFieldMessage('oldPassword', '現在のパスワードは文字で入力してください');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('password', 'password')
             ->setAttribs([
                 'autocomplete' => 'new-password',
                 'id' => 'new-password',
                 'minlength' => 8,
                 'maxlength' => 20,
                 'pattern' => '^[\x20-\x7E]+$', // ASCII 文字列
                 'placeholder' => '',
                 'required' => 'required',
                 'title' => '新しいパスワードは8文字以上20文字以下の英数字記号(!@#$%^&*)で入力してください',
             ]);
        /** @psalm-suppress TooManyArguments */
        $this->filter
            ->validate('password')
            ->isNotBlank();
        /** @psalm-suppress TooManyArguments */
        $this->filter
            ->validate('password')
            ->is('string');
        /** @psalm-suppress TooManyArguments */
        $this->filter
            ->validate('password')
            ->is('regex', '/^[A-Za-z0-9!@#$%^&*]+$/i');
        /** @psalm-suppress TooManyArguments */
        $this->filter
            ->validate('password')
            ->is('strlenBetween', 8, 20);
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('password')->isNot('equalToField', 'oldPassword');
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('password')
            ->is('callback', function (stdClass $subject, string $field) {
                return $this->badPasswordQuery->item($subject->$field) === null;
            });
        $this->filter->useFieldMessage('password', '新しいパスワードは8文字以上20文字以下の英数字記号(!@#$%^&*)で入力してください');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('passwordConfirmation', 'password')
             ->setAttribs([
                 'autocomplete' => 'new-password',
                 'minlength' => 8,
                 'maxlength' => 20,
                 'pattern' => '^[\x20-\x7E]+$', // ASCII 文字列
                 'placeholder' => '',
                 'required' => 'required',
                 'title' => '新しいパスワードは8文字以上20文字以下の英数字記号(!@#$%^&*)で入力してください',
             ]);
        $this->filter->validate('passwordConfirmation')->isNotBlank();
        $this->filter->validate('passwordConfirmation')->is('string');
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('passwordConfirmation')->is('regex', '/^[A-Za-z0-9!@#$%^&*]+$/i');
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('passwordConfirmation')->is('strlenBetween', 8, 20);
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('passwordConfirmation')->is('equalToField', 'password');
        $this->filter->useFieldMessage('passwordConfirmation', '新しいパスワードは8文字以上20文字以下の英数字記号(!@#$%^&*)で入力してください');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('update', 'submit');
    }
}

<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form\Admin;

use AppCore\Infrastructure\Query\AdminQueryInterface;
use MyVendor\MyProject\Form\AntiCsrfSetter;
use MyVendor\MyProject\Form\ExtendedForm;
use Ray\Di\Di\Inject;
use stdClass;

/** @psalm-suppress PropertyNotSetInConstructor */
class AdminJoinForm extends ExtendedForm
{
    use AntiCsrfSetter;

    private AdminQueryInterface $adminQuery;

    #[Inject]
    public function setAdminQuery(AdminQueryInterface $adminQuery): void
    {
        $this->adminQuery = $adminQuery;
    }

    public function init(): void
    {
        /** @psalm-suppress UndefinedMethod */
        $this->setField('emailAddress', 'email')
             ->setAttribs([
                 'autofocus' => '',
                 'autocomplete' => 'email',
                 'placeholder' => '',
                 'required' => 'required',
                 'title' => '有効なメールアドレスを入力してください',
             ]);
        $this->filter->validate('emailAddress')->is('email');
        /** @psalm-suppress TooManyArguments */
        $this->filter->validate('emailAddress')
            ->is('callback', function (stdClass $subject, string $field) {
                return $this->adminQuery->itemByEmailAddress($subject->$field) === null;
            });
        $this->filter->useFieldMessage('emailAddress', '有効なメールアドレスを入力してください');

        /** @psalm-suppress UndefinedMethod */
        $this->setField('continue', 'submit');
    }
}

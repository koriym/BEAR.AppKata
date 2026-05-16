<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin;

use AppCore\Domain\Auth\AuthenticationException;
use MyVendor\MyProject\Annotation\AdminGuard;
use MyVendor\MyProject\Annotation\AdminVerifyPassword;
use MyVendor\MyProject\InputQuery\Admin\UserPasswordInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

class PasswordConfirm extends BaseAdminPage
{
    public function __construct(
        #[Named('admin_password_confirm_form')]
        protected readonly FormInterface $form,
    ) {
        $this->body['form'] = $this->form;
    }

    #[AdminGuard]
    public function onGet(): static
    {
        return $this;
    }

    /** @SuppressWarnings("PHPMD.UnusedFormalParameter") */
    #[FormValidation]
    #[AdminGuard]
    #[AdminVerifyPassword]
    public function onPost(
        #[Input]
        UserPasswordInput $input,
    ): static {
        // password confirm success !!

        return $this;
    }

    public function onPostValidationFailed(): static
    {
        return $this;
    }

    /**
     * Callback from AdminAuthentication
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function onPostAuthenticationFailed(AuthenticationException $authException): static
    {
        $this->body['authError'] = true;

        return $this;
    }
}

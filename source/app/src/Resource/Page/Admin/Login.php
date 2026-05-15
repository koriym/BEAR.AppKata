<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin;

use AppCore\Domain\Auth\AuthenticationException;
use AppCore\Domain\Captcha\CaptchaException;
use MyVendor\MyProject\Annotation\AdminLogin;
use MyVendor\MyProject\Annotation\CloudflareTurnstile;
use MyVendor\MyProject\InputQuery\Admin\LoginUserInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

class Login extends BaseAdminPage
{
    public function __construct(
        #[Named('admin_login_form')]
        protected readonly FormInterface $form,
    ) {
        $this->body['form'] = $this->form;
    }

    public function onGet(): static
    {
        return $this;
    }

    /** @SuppressWarnings("PHPMD.UnusedFormalParameter") */
    #[FormValidation]
    #[CloudflareTurnstile]
    #[AdminLogin]
    public function onPost(
        #[Input]
        LoginUserInput $input,
    ): static {
        // login success !!

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

    /**
     * Callback from CloudflareTurnstileVerification
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function onCfTurnstileFailed(
        LoginUserInput $input,
        CaptchaException $captchaException,
    ): static {
        $this->body['captchaError'] = true;

        return $this;
    }
}

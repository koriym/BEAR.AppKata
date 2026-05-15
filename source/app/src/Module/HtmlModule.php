<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Module;

use AppCore\Domain\Auth\AdminContextInterface;
use AppCore\Presentation\Shared\AdminContext;
use BEAR\Package\AbstractAppModule;
use MyVendor\MyProject\Form\Admin as AdminForm;
use MyVendor\MyProject\Form\Customer as CustomerForm;
use MyVendor\MyProject\TemplateEngine\QiqCustomHelpers;
use MyVendor\MyProject\TemplateEngine\QiqModule;
use Qiq\Helpers;
use Ray\WebFormModule\AuraInputModule;
use Ray\WebFormModule\FormInterface;

use function getenv;
use function time;

/** @SuppressWarnings("PHPMD.CouplingBetweenObjects") */
class HtmlModule extends AbstractAppModule
{
    protected function configure(): void
    {
        $this->install(new AuraInputModule());
        $this->bind(Helpers::class)->to(QiqCustomHelpers::class);
        $this->install(
            new QiqModule(
                [$this->appMeta->appDir . '/var/qiq/template'],
                [
                    'cloudflareTurnstileSiteKey' => (string) getenv('CLOUDFLARE_TURNSTILE_SITE_KEY'),
                    'compiledTime' => time(),
                ],
            ),
        );
        $this->install(new SessionAuthModule());
        $this->install(new CaptchaModule());
        $this->install(new ThrottlingModule());

        $this->admin();
        $this->customer();
    }

    private function admin(): void
    {
        //@formatter:off
        $this->bind(AdminContextInterface::class)->to(AdminContext::class);

        $this->bind(FormInterface::class)->annotatedWith('admin_code_verify_form')->to(AdminForm\AdminCodeVerifyForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_delete_form')->to(AdminForm\AdminDeleteForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_email_create_form')->to(AdminForm\AdminEmailCreateForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_forgot_password_form')->to(AdminForm\AdminForgotPasswordForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_join_form')->to(AdminForm\AdminJoinForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_login_form')->to(AdminForm\AdminLoginForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_password_confirm_form')->to(AdminForm\AdminPasswordConfirmForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_password_reset_form')->to(AdminForm\AdminPasswordResetForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_password_update_form')->to(AdminForm\AdminPasswordUpdateForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_sign_up_form')->to(AdminForm\AdminSignUpForm::class);

        $this->bind(FormInterface::class)->annotatedWith('admin_upload_demo_form')->to(AdminForm\AdminUploadDemoForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_fieldset_demo_form')->to(AdminForm\AdminFieldsetDemoForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_multiple_demo_form')->to(AdminForm\AdminMultipleDemoForm::class);
        $this->bind(FormInterface::class)->annotatedWith('admin_contact_demo_form')->to(AdminForm\AdminContactDemoForm::class);
        //@formatter:on
    }

    private function customer(): void
    {
        $this->bind(FormInterface::class)->annotatedWith('user_login_form')->to(CustomerForm\UserLoginForm::class);
    }
}

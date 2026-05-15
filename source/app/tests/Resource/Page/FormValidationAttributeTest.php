<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page;

use MyVendor\MyProject\Resource\Page\Admin\CodeVerify;
use MyVendor\MyProject\Resource\Page\Admin\ContactDemo;
use MyVendor\MyProject\Resource\Page\Admin\FieldsetDemo;
use MyVendor\MyProject\Resource\Page\Admin\ForgotPassword;
use MyVendor\MyProject\Resource\Page\Admin\Join;
use MyVendor\MyProject\Resource\Page\Admin\Login as AdminLogin;
use MyVendor\MyProject\Resource\Page\Admin\MultipleDemo;
use MyVendor\MyProject\Resource\Page\Admin\PasswordConfirm;
use MyVendor\MyProject\Resource\Page\Admin\ResetPassword;
use MyVendor\MyProject\Resource\Page\Admin\Settings\Delete as SettingsDelete;
use MyVendor\MyProject\Resource\Page\Admin\Settings\Emails;
use MyVendor\MyProject\Resource\Page\Admin\Settings\Password;
use MyVendor\MyProject\Resource\Page\Admin\SignUp;
use MyVendor\MyProject\Resource\Page\Admin\UploadDemo;
use MyVendor\MyProject\Resource\Page\User\Login as UserLogin;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ray\WebFormModule\Annotation\FormValidation;
use ReflectionClass;

final class FormValidationAttributeTest extends TestCase
{
    /** @return iterable<string, array{class-string}> */
    public static function formPages(): iterable
    {
        yield 'admin-code-verify' => [CodeVerify::class];
        yield 'admin-contact-demo' => [ContactDemo::class];
        yield 'admin-fieldset-demo' => [FieldsetDemo::class];
        yield 'admin-forgot-password' => [ForgotPassword::class];
        yield 'admin-join' => [Join::class];
        yield 'admin-login' => [AdminLogin::class];
        yield 'admin-multiple-demo' => [MultipleDemo::class];
        yield 'admin-password-confirm' => [PasswordConfirm::class];
        yield 'admin-reset-password' => [ResetPassword::class];
        yield 'admin-settings-delete' => [SettingsDelete::class];
        yield 'admin-settings-emails' => [Emails::class];
        yield 'admin-settings-password' => [Password::class];
        yield 'admin-sign-up' => [SignUp::class];
        yield 'admin-upload-demo' => [UploadDemo::class];
        yield 'user-login' => [UserLogin::class];
    }

    /** @param class-string $pageClass */
    #[DataProvider('formPages')]
    public function testFormValidationUsesPhpAttribute(string $pageClass): void
    {
        $method = (new ReflectionClass($pageClass))->getMethod('onPost');

        $this->assertCount(1, $method->getAttributes(FormValidation::class));
        $this->assertStringNotContainsString('@FormValidation', (string) $method->getDocComment());
    }
}

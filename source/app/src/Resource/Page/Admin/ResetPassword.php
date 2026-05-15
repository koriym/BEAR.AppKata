<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin;

use AppCore\Application\Admin\GetForgotAdminPasswordInputData;
use AppCore\Application\Admin\GetForgotAdminPasswordUseCase;
use AppCore\Application\Admin\ResetAdminPasswordInputData;
use AppCore\Application\Admin\ResetAdminPasswordUseCase;
use BEAR\Resource\NullRenderer;
use Koriym\HttpConstants\ResponseHeader;
use Koriym\HttpConstants\StatusCode;
use MyVendor\MyProject\Form\ExtendedForm;
use MyVendor\MyProject\InputQuery\Admin\ResetPasswordInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\AuraSqlModule\Annotation\Transactional;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;
use Throwable;

use function assert;

class ResetPassword extends BaseAdminPage
{
    /** @SuppressWarnings("PHPMD.LongVariable") */
    public function __construct(
        #[Named('admin_password_reset_form')]
        protected readonly FormInterface $form,
        private readonly GetForgotAdminPasswordUseCase $getForgotAdminPasswordUseCase,
        private readonly ResetAdminPasswordUseCase $resetAdminPasswordUseCase,
    ) {
        $this->body['form'] = $this->form;
    }

    /** @SuppressWarnings("PHPMD.LongVariable") */
    public function onGet(
        #[Input]
        string $signature,
    ): static {
        try {
            $this->getForgotAdminPasswordUseCase->execute(
                new GetForgotAdminPasswordInputData($signature),
            );
        } catch (Throwable) {
            $this->context->setSessionValue('error:message', 'message:admin:reset_password:decrypt_error');
            $this->context->setSessionValue('error:returnName', 'Forgot password');
            $this->context->setSessionValue('error:returnUrl', '/admin/forgot-password');
            $this->renderer = new NullRenderer();
            $this->code = StatusCode::SEE_OTHER;
            $this->headers = [ResponseHeader::LOCATION => '/admin/error'];

            return $this;
        }

        assert($this->form instanceof ExtendedForm);
        $this->form->fill(['signature' => $signature]);

        return $this;
    }

    /** @SuppressWarnings("PHPMD.LongVariable") */
    #[FormValidation]
    #[Transactional]
    public function onPost(
        #[Input]
        ResetPasswordInput $input,
    ): static {
        $this->resetAdminPasswordUseCase->execute(
            new ResetAdminPasswordInputData(
                $input->password,
                $input->signature,
            ),
        );

        $this->renderer = new NullRenderer();
        $this->code = StatusCode::SEE_OTHER;
        $this->headers = [ResponseHeader::LOCATION => '/admin/login']; // 注意：フォームがある画面に戻るとフラッシュメッセージが表示されない

        return $this;
    }

    public function onPostValidationFailed(): static
    {
        return $this;
    }
}

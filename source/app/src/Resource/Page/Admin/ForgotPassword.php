<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin;

use AppCore\Application\Admin\ForgotAdminPasswordInputData;
use AppCore\Application\Admin\ForgotAdminPasswordUseCase;
use AppCore\Domain\Captcha\CaptchaException;
use BEAR\Resource\NullRenderer;
use Koriym\HttpConstants\ResponseHeader;
use Koriym\HttpConstants\StatusCode;
use MyVendor\MyProject\Annotation\CloudflareTurnstile;
use MyVendor\MyProject\Annotation\RateLimiter;
use MyVendor\MyProject\InputQuery\Admin\ForgotPasswordInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\AuraSqlModule\Annotation\Transactional;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

class ForgotPassword extends BaseAdminPage
{
    /** @SuppressWarnings("PHPMD.LongVariable") */
    public function __construct(
        private readonly ForgotAdminPasswordUseCase $forgotAdminPasswordUseCase,
        #[Named('admin_forgot_password_form')]
        protected readonly FormInterface $form,
    ) {
        $this->body['form'] = $this->form;
    }

    public function onGet(): static
    {
        return $this;
    }

    /** @SuppressWarnings("PHPMD.LongVariable") */
    #[FormValidation]
    #[CloudflareTurnstile]
    #[RateLimiter]
    #[Transactional]
    public function onPost(
        #[Input]
        ForgotPasswordInput $input,
    ): static {
        $outputData = $this->forgotAdminPasswordUseCase->execute(
            new ForgotAdminPasswordInputData($input->emailAddress),
        );

        $this->renderer = new NullRenderer();
        $this->code = StatusCode::SEE_OTHER;
        $this->headers = [ResponseHeader::LOCATION => $outputData->redirectUrl]; // 注意：フォームがある画面に戻るとフラッシュメッセージが表示されない

        return $this;
    }

    public function onPostValidationFailed(): static
    {
        return $this;
    }

    /**
     * Callback from CloudflareTurnstileVerification
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function onCfTurnstileFailed(
        ForgotPasswordInput $input,
        CaptchaException $captchaException,
    ): static {
        $this->body['captchaError'] = true;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin;

use AppCore\Application\GetVerificationCodeInputData;
use AppCore\Application\GetVerificationCodeUseCase;
use AppCore\Application\VerifyVerificationCodeInputData;
use AppCore\Application\VerifyVerificationCodeUseCase;
use AppCore\Domain\VerificationCode\VerificationCodeNotFoundException;
use BEAR\Resource\NullRenderer;
use Koriym\HttpConstants\ResponseHeader;
use Koriym\HttpConstants\StatusCode;
use MyVendor\MyProject\Form\ExtendedForm;
use MyVendor\MyProject\InputQuery\Admin\CodeVerifyInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\AuraSqlModule\Annotation\Transactional;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

use function assert;

class CodeVerify extends BaseAdminPage
{
    /** @SuppressWarnings("PHPMD.LongVariable") */
    public function __construct(
        #[Named('admin_code_verify_form')]
        protected readonly FormInterface $form,
        protected readonly GetVerificationCodeUseCase $getAdminVerificationCodeUseCase,
        protected readonly VerifyVerificationCodeUseCase $verifyAdminVerificationCodeUseCase,
    ) {
        $this->body['form'] = $this->form;
    }

    public function onGet(
        #[Input]
        string $uuid,
    ): static {
        try {
            $outputData = $this->getAdminVerificationCodeUseCase->execute(
                new GetVerificationCodeInputData($uuid),
            );
        } catch (VerificationCodeNotFoundException) {
            $this->renderer = new NullRenderer();
            $this->code = StatusCode::SEE_OTHER;
            $this->headers = [ResponseHeader::LOCATION => '/admin/login']; // 注意：フォームがある画面に戻るとフラッシュメッセージが表示されない

            return $this;
        }

        assert($this->form instanceof ExtendedForm);
        $this->form->fill(['uuid' => $outputData->uuid]);

        return $this;
    }

    #[FormValidation]
    #[Transactional]
    public function onPost(
        #[Input]
        CodeVerifyInput $input,
    ): static {
        $this->renderer = new NullRenderer();
        $this->code = StatusCode::SEE_OTHER;

        try {
            $outputData = $this->verifyAdminVerificationCodeUseCase->execute(
                new VerifyVerificationCodeInputData(
                    $input->uuid,
                    $input->code,
                ),
            );
        } catch (VerificationCodeNotFoundException) {
            $this->renderer = new NullRenderer();
            $this->code = StatusCode::SEE_OTHER;
            $this->headers = [ResponseHeader::LOCATION => '/admin/login']; // 注意：フォームがある画面に戻るとフラッシュメッセージが表示されない

            return $this;
        }

        $this->headers = [ResponseHeader::LOCATION => $outputData->url]; // 注意：フォームがある画面に戻るとフラッシュメッセージが表示されない

        return $this;
    }

    public function onPostValidationFailed(): static
    {
        return $this;
    }
}

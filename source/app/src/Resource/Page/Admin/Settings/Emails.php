<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin\Settings;

use AppCore\Application\Admin\CreateAdminEmailInputData;
use AppCore\Application\Admin\CreateAdminEmailUseCase;
use AppCore\Application\Admin\GetAdminInputData;
use AppCore\Application\Admin\GetAdminUseCase;
use AppCore\Domain\AccessControl\Permission;
use AppCore\Domain\Auth\AdminAuthenticatorInterface;
use AppCore\Domain\Language\LanguageInterface;
use BEAR\Resource\NullRenderer;
use Koriym\HttpConstants\ResponseHeader;
use Koriym\HttpConstants\StatusCode;
use MyVendor\MyProject\Annotation\AdminGuard;
use MyVendor\MyProject\Annotation\RequiredPermission;
use MyVendor\MyProject\InputQuery\Admin\CreateEmailInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\AuraSqlModule\Annotation\Transactional;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

class Emails extends BaseAdminPage
{
    /** @SuppressWarnings("PHPMD.LongVariable") */
    public function __construct(
        private readonly AdminAuthenticatorInterface $adminAuthenticator,
        private readonly CreateAdminEmailUseCase $createAdminEmailUseCase,
        private readonly GetAdminUseCase $getAdminUseCase,
        #[Named('admin_email_create_form')]
        protected readonly FormInterface $form,
        private readonly LanguageInterface $language,
    ) {
        $this->body['form'] = $this->form;
    }

    #[AdminGuard]
    #[RequiredPermission('settings', Permission::Read)]
    public function onGet(): static
    {
        $adminId = $this->adminAuthenticator->getUserId();
        if ($adminId === null) {
            return $this;
        }

        $outputData = $this->getAdminUseCase->execute(
            new GetAdminInputData($adminId),
        );

        $this->body['admin'] = $outputData->admin;

        return $this;
    }

    #[FormValidation]
    #[AdminGuard]
    #[Transactional]
    public function onPost(
        #[Input]
        CreateEmailInput $input,
    ): static {
        $adminId = $this->adminAuthenticator->getUserId();
        if ($adminId === null) {
            return $this;
        }

        $this->createAdminEmailUseCase->execute(
            new CreateAdminEmailInputData(
                $adminId,
                $input->emailAddress,
            ),
        );

        $this->renderer = new NullRenderer();
        $this->code = StatusCode::SEE_OTHER;
        $this->headers = [ResponseHeader::LOCATION => '/admin/settings/index']; // 注意：フォームがある画面に戻るとフラッシュメッセージが表示されない
        $this->context->setFlashMessage($this->language->get('message:admin:email_created'));

        return $this;
    }

    /** @SuppressWarnings("PHPMD.UnusedFormalParameter") */
    public function onPostValidationFailed(): static
    {
        $adminId = $this->adminAuthenticator->getUserId();
        if ($adminId === null) {
            return $this;
        }

        $outputData = $this->getAdminUseCase->execute(
            new GetAdminInputData($adminId),
        );

        $this->body['admin'] = $outputData->admin;

        return $this;
    }
}

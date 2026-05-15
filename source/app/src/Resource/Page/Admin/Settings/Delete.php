<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin\Settings;

use AppCore\Application\Admin\DeleteAdminInputData;
use AppCore\Application\Admin\DeleteAdminUseCase;
use AppCore\Domain\AccessControl\Permission;
use AppCore\Domain\Auth\AdminAuthenticatorInterface;
use MyVendor\MyProject\Annotation\AdminGuard;
use MyVendor\MyProject\Annotation\AdminLogout;
use MyVendor\MyProject\Annotation\AdminPasswordProtect;
use MyVendor\MyProject\Annotation\RequiredPermission;
use MyVendor\MyProject\InputQuery\Admin\DeleteInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\AuraSqlModule\Annotation\Transactional;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

/** @SuppressWarnings("PHPMD.CouplingBetweenObjects") */
class Delete extends BaseAdminPage
{
    public function __construct(
        private readonly AdminAuthenticatorInterface $adminAuthenticator,
        private readonly DeleteAdminUseCase $deleteAdminUseCase,
        #[Named('admin_delete_form')]
        protected readonly FormInterface $form,
    ) {
        $this->body['form'] = $this->form;
    }

    #[AdminGuard]
    #[AdminPasswordProtect]
    #[RequiredPermission('settings', Permission::Read)]
    public function onGet(): static
    {
        return $this;
    }

    /** @SuppressWarnings("PHPMD.UnusedFormalParameter") */
    #[FormValidation]
    #[AdminGuard]
    #[Transactional]
    #[AdminLogout]
    public function onPost(
        #[Input]
        DeleteInput $input,
    ): static {
        $this->deleteAdminUseCase->execute(
            new DeleteAdminInputData((int) $this->adminAuthenticator->getUserId()),
        );

        return $this;
    }

    public function onPostValidationFailed(): static
    {
        return $this;
    }
}

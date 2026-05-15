<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin;

use Koriym\FileUpload\ErrorFileUpload;
use Koriym\FileUpload\FileUpload;
use MyVendor\MyProject\Annotation\AdminGuard;
use MyVendor\MyProject\InputQuery\Admin\UploadDemoInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

class UploadDemo extends BaseAdminPage
{
    public function __construct(
        #[Named('admin_upload_demo_form')]
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
    public function onPost(
        #[Input]
        UploadDemoInput $input,
    ): static {
        if ($input->file instanceof FileUpload) {
            $this->body['fileUpload'] = $input->file;
        }

        if ($input->file instanceof ErrorFileUpload) {
            $this->body['errorFileUpload'] = $input->file;
        }

        return $this;
    }

    public function onPostValidationFailed(): static
    {
        return $this;
    }
}

<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin;

use MyVendor\MyProject\InputQuery\Admin\MultipleDemoInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

class MultipleDemo extends BaseAdminPage
{
    public function __construct(
        #[Named('admin_multiple_demo_form')]
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
    public function onPost(
        #[Input]
        MultipleDemoInput $input,
    ): static {
        return $this;
    }

    public function onPostValidationFailed(): static
    {
        return $this;
    }
}

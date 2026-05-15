<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin;

use MyVendor\MyProject\Form\ExtendedForm;
use MyVendor\MyProject\InputQuery\Admin\FieldsetDemoInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

use function assert;

class FieldsetDemo extends BaseAdminPage
{
    public function __construct(
        #[Named('admin_fieldset_demo_form')]
        protected readonly FormInterface $form,
    ) {
        $this->body['form'] = $this->form;
    }

    public function onGet(): static
    {
        assert($this->form instanceof ExtendedForm);
        $this->form->fill([
            'deliveries' => [[], []],
        ]);

        return $this;
    }

    /** @SuppressWarnings("PHPMD.UnusedFormalParameter") */
    #[FormValidation]
    public function onPost(
        #[Input]
        FieldsetDemoInput $input,
    ): static {
        return $this;
    }

    /** @SuppressWarnings("PHPMD.UnusedFormalParameter") */
    public function onPostValidationFailed(
        #[Input]
        FieldsetDemoInput $input,
    ): static {
        return $this;
    }
}

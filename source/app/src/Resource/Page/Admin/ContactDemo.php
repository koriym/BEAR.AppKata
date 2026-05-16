<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page\Admin;

use Koriym\HttpConstants\StatusCode;
use MyVendor\MyProject\Form\Admin\AdminContactDemoForm;
use MyVendor\MyProject\Form\FormStep;
use MyVendor\MyProject\InputQuery\Admin\ContactDemoInput;
use MyVendor\MyProject\Resource\Page\BaseAdminPage;
use Ray\Di\Di\Named;
use Ray\InputQuery\Attribute\Input;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

use function assert;

class ContactDemo extends BaseAdminPage
{
    public function __construct(
        #[Named('admin_contact_demo_form')]
        protected readonly FormInterface $form,
    ) {
        $this->body['form'] = $this->form;
        $this->body['inputStep'] = FormStep::Input;
        $this->body['confirmStep'] = FormStep::Confirm;
        $this->body['completeStep'] = FormStep::Complete;
    }

    public function onGet(): static
    {
        return $this;
    }

    /** @SuppressWarnings("PHPMD.UnusedFormalParameter") */
    #[FormValidation]
    public function onPost(
        #[Input]
        ContactDemoInput $input,
    ): static {
        if ($input->mode === FormStep::Complete->name) {
            $this->code = StatusCode::SEE_OTHER;
            $this->headers = ['Location' => '/admin/contact-complete-demo'];

            return $this;
        }

        return $this;
    }

    public function onPostValidationFailed(): static
    {
        assert($this->form instanceof AdminContactDemoForm);
        $this->form->mode = 'INPUT';

        return $this;
    }
}

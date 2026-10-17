<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Interceptor;

use BEAR\Resource\ResourceObject;
use Override;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\AbstractForm;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;
use Ray\WebFormModule\FailureHandlerInterface;
use ReflectionProperty;

use function assert;

/**
 * Applies the form named by #[FormValidation] to the request parameters of the resource URI.
 *
 * Replaces Ray\WebFormModule\AuraInputInterceptor, which reads $_POST.
 */
final readonly class FormValidationInterceptor implements MethodInterceptor
{
    public function __construct(
        private FailureHandlerInterface $failureHandler,
    ) {
    }

    #[Override]
    public function invoke(MethodInvocation $invocation): mixed
    {
        $ro = $invocation->getThis();
        assert($ro instanceof ResourceObject);

        $attributes = $invocation->getMethod()->getAttributes(FormValidation::class);
        assert($attributes !== []);
        $validation = $attributes[0]->newInstance();

        $form = $this->form($ro, $validation->form);
        if ($form->apply($ro->uri->query)) {
            return $invocation->proceed();
        }

        return $this->failureHandler->handle($validation, $invocation, $form);
    }

    private function form(ResourceObject $ro, string $property): AbstractForm
    {
        $form = (new ReflectionProperty($ro, $property))->getValue($ro);
        if (! $form instanceof AbstractForm) {
            throw new InvalidFormPropertyException($property);
        }

        return $form;
    }
}

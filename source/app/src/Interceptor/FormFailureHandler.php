<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Interceptor;

use MyVendor\MyProject\Annotation\FormValidation;
use Override;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\AbstractForm;
use Ray\WebFormModule\Annotation\AbstractValidation;
use Ray\WebFormModule\Exception\InvalidOnFailureMethod;
use Ray\WebFormModule\FailureHandlerInterface;

use function method_exists;

final class FormFailureHandler implements FailureHandlerInterface
{
    private const FAILURE_SUFFIX = 'ValidationFailed';

    /**
     * @param MethodInvocation<object> $invocation
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    #[Override]
    public function handle(AbstractValidation $formValidation, MethodInvocation $invocation, AbstractForm $form): mixed
    {
        $object = $invocation->getThis();
        if (! $formValidation instanceof FormValidation) {
            throw new InvalidOnFailureMethod($object::class);
        }

        $onFailureMethod = $formValidation->onFailure ?: $invocation->getMethod()->getName() . self::FAILURE_SUFFIX;
        if (! method_exists($object, $onFailureMethod)) {
            throw new InvalidOnFailureMethod($object::class);
        }

        return $object->{$onFailureMethod}(...$invocation->getArguments());
    }
}

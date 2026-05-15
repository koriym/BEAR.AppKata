<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Interceptor;

use MyVendor\MyProject\Annotation\FormValidation;
use Override;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\WebFormModule\AbstractForm;
use Ray\WebFormModule\AntiCsrf;
use Ray\WebFormModule\Exception\InvalidArgumentException;
use Ray\WebFormModule\Exception\InvalidFormPropertyException;
use Ray\WebFormModule\FailureHandlerInterface;
use Ray\WebFormModule\SubmitInterface;
use ReflectionClass;

use function array_shift;
use function is_array;
use function property_exists;

final readonly class FormValidationInterceptor implements MethodInterceptor
{
    public function __construct(
        private FailureHandlerInterface $failureHandler,
    ) {
    }

    #[Override]
    public function invoke(MethodInvocation $invocation): mixed
    {
        $formValidation = $invocation->getMethod()->getAnnotation(FormValidation::class);
        if (! $formValidation instanceof FormValidation) {
            return $invocation->proceed();
        }

        $form = $this->getFormProperty($formValidation, $invocation->getThis());
        $data = $form instanceof SubmitInterface ? $form->submit() : $this->getNamedArguments($invocation);
        if (! is_array($data)) {
            throw new InvalidArgumentException('Submitted form data must be an array.');
        }

        if ($form->apply($data)) {
            return $invocation->proceed();
        }

        return $this->failureHandler->handle($formValidation, $invocation, $form);
    }

    /**
     * @param MethodInvocation<object> $invocation
     *
     * @return array<string, mixed>
     *
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    private function getNamedArguments(MethodInvocation $invocation): array
    {
        $submit = [];
        $params = $invocation->getMethod()->getParameters();
        $args = $invocation->getArguments()->getArrayCopy();
        foreach ($params as $param) {
            $submit[$param->getName()] = array_shift($args);
        }

        if (isset($_POST[AntiCsrf::TOKEN_KEY])) {
            $submit[AntiCsrf::TOKEN_KEY] = $_POST[AntiCsrf::TOKEN_KEY];
        }

        return $submit;
    }

    private function getFormProperty(FormValidation $formValidation, object $object): AbstractForm
    {
        if (! property_exists($object, $formValidation->form)) {
            throw new InvalidFormPropertyException($formValidation->form);
        }

        $form = (new ReflectionClass($object))->getProperty($formValidation->form)->getValue($object);
        if (! $form instanceof AbstractForm) {
            throw new InvalidFormPropertyException($formValidation->form);
        }

        return $form;
    }
}

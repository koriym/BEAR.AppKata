<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Module;

use Aura\Filter\FilterFactory;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;
use Aura\Input\BuilderInterface;
use Aura\Input\Filter;
use Aura\Input\FilterInterface;
use MyVendor\MyProject\Interceptor\FormValidationInterceptor;
use Override;
use Ray\Di\AbstractModule;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FailureHandlerInterface;
use Ray\WebFormModule\OnFailureMethodHandler;

/** Aura.Input form validation driven by #[FormValidation], fed from the resource URI instead of $_POST. */
final class FormModule extends AbstractModule
{
    #[Override]
    protected function configure(): void
    {
        $this->bind(BuilderInterface::class)->to(Builder::class);
        $this->bind(FilterInterface::class)->to(Filter::class);
        $this->bind(HelperLocatorFactory::class);
        $this->bind(FilterFactory::class);
        $this->bind(FailureHandlerInterface::class)->to(OnFailureMethodHandler::class);
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(FormValidation::class),
            [FormValidationInterceptor::class],
        );
    }
}

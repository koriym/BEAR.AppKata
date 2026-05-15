<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Module;

use Aura\Filter\FilterFactory;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\AntiCsrfInterface;
use Aura\Input\Builder;
use Aura\Input\BuilderInterface;
use Aura\Input\Filter;
use Aura\Input\FilterInterface;
use BEAR\Resource\ResourceObject;
use MyVendor\MyProject\Annotation\FormValidation;
use MyVendor\MyProject\Interceptor\FormFailureHandler;
use MyVendor\MyProject\Interceptor\FormValidationInterceptor;
use Override;
use Ray\AuraSessionModule\AuraSessionModule;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;
use Ray\WebFormModule\AntiCsrf;
use Ray\WebFormModule\FailureHandlerInterface;

/** @SuppressWarnings("PHPMD.CouplingBetweenObjects") */
final class FormModule extends AbstractModule
{
    #[Override]
    protected function configure(): void
    {
        $this->install(new AuraSessionModule());
        $this->bind(BuilderInterface::class)->to(Builder::class);
        $this->bind(FilterInterface::class)->to(Filter::class);
        $this->bind(AntiCsrfInterface::class)->to(AntiCsrf::class)->in(Scope::SINGLETON);
        $this->bind(FailureHandlerInterface::class)->to(FormFailureHandler::class);
        $this->bind(HelperLocatorFactory::class);
        $this->bind(FilterFactory::class);
        $this->bindInterceptor(
            $this->matcher->subclassesOf(ResourceObject::class),
            $this->matcher->annotatedWith(FormValidation::class),
            [FormValidationInterceptor::class],
        );
    }
}

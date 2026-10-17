<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use Aura\Input\AntiCsrfInterface;
use Aura\Session\Phpfunc;
use Override;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;
use Ray\WebFormModule\AntiCsrf;

/**
 * Enforces CSRF validation under the CLI SAPI, where AntiCsrf would otherwise accept every request.
 */
final class CsrfEnforcedModule extends AbstractModule
{
    #[Override]
    protected function configure(): void
    {
        $this->bind()->annotatedWith('csrf_is_cli')->toInstance(false);
        $this->bind(AntiCsrfInterface::class)
            ->toConstructor(AntiCsrf::class, ['isCli' => 'csrf_is_cli'])
            ->in(Scope::SINGLETON);
        $this->bind(Phpfunc::class)->to(FakeSessionPhpfunc::class)->in(Scope::SINGLETON);
    }
}

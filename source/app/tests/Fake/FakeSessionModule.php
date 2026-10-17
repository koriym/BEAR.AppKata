<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use Aura\Session\Phpfunc;
use Override;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

/** Lets Aura.Session run on $_SESSION under PHPUnit. */
final class FakeSessionModule extends AbstractModule
{
    #[Override]
    protected function configure(): void
    {
        $this->bind(Phpfunc::class)->to(FakeSessionPhpfunc::class)->in(Scope::SINGLETON);
    }
}

<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Module;

use Aura\Session\CsrfToken;
use MyVendor\MyProject\Interceptor\CsrfGuard;
use MyVendor\MyProject\Module\Matcher\PageResourceMatcher;
use MyVendor\MyProject\Module\Matcher\UnsafeMethodMatcher;
use MyVendor\MyProject\Provider\CsrfTokenProvider;
use Override;
use Ray\AuraSessionModule\AuraSessionModule;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

/**
 * Every state-changing page request must carry the session CSRF token.
 * Resources declare nothing; the policy lives here. The guard is bound with priority
 * so that it runs before authentication and form validation, whatever their binding order.
 */
final class CsrfModule extends AbstractModule
{
    #[Override]
    protected function configure(): void
    {
        $this->install(new AuraSessionModule());
        $this->bind(CsrfToken::class)->toProvider(CsrfTokenProvider::class)->in(Scope::SINGLETON);
        $this->bindPriorityInterceptor(
            new PageResourceMatcher(),
            new UnsafeMethodMatcher(),
            [CsrfGuard::class],
        );
    }
}

<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Provider;

use Aura\Session\CsrfToken;
use Aura\Session\Session;
use Override;
use Ray\Di\ProviderInterface;

/** @implements ProviderInterface<CsrfToken> */
final readonly class CsrfTokenProvider implements ProviderInterface
{
    public function __construct(
        private Session $session,
    ) {
    }

    #[Override]
    public function get(): CsrfToken
    {
        return $this->session->getCsrfToken();
    }
}

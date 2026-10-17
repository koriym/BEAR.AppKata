<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Interceptor;

use Aura\Session\CsrfToken;
use BEAR\Resource\ResourceObject;
use MyVendor\MyProject\Exception\CsrfViolationException;
use Override;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

use function assert;
use function is_string;

/**
 * Validates the CSRF token of a state-changing page request.
 *
 * The token travels as an ordinary request parameter and is read from the resource URI,
 * so the guard works wherever BEAR routes a request from (HTTP, CLI, tests).
 */
final readonly class CsrfGuard implements MethodInterceptor
{
    public const string FIELD = '__csrf_token';

    public function __construct(
        private CsrfToken $csrfToken,
    ) {
    }

    #[Override]
    public function invoke(MethodInvocation $invocation): mixed
    {
        $ro = $invocation->getThis();
        assert($ro instanceof ResourceObject);

        $token = $ro->uri->query[self::FIELD] ?? null;
        if (! is_string($token) || ! $this->csrfToken->isValid($token)) {
            throw new CsrfViolationException();
        }

        return $invocation->proceed();
    }
}

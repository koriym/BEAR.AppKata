<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Module\Matcher;

use Override;
use Ray\Aop\AbstractMatcher;
use ReflectionClass;
use ReflectionMethod;

use function str_starts_with;

/**
 * Matches every resource under the Page namespace, which is the HTTP-facing surface of the HTML app.
 *
 * @SuppressWarnings("PHPMD.UnusedFormalParameter")
 */
final class PageResourceMatcher extends AbstractMatcher
{
    private const string NAMESPACE_PREFIX = 'MyVendor\MyProject\Resource\Page\\';

    /**
     * @param ReflectionClass<object> $class
     * @param array<array-key, mixed> $arguments
     */
    #[Override]
    public function matchesClass(ReflectionClass $class, array $arguments): bool
    {
        return str_starts_with($class->getName(), self::NAMESPACE_PREFIX);
    }

    /** @param array<array-key, mixed> $arguments */
    #[Override]
    public function matchesMethod(ReflectionMethod $method, array $arguments): bool
    {
        return false;
    }
}

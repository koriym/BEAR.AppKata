<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Module\Matcher;

use Override;
use Ray\Aop\AbstractMatcher;
use ReflectionClass;
use ReflectionMethod;

use function in_array;

/**
 * Matches the resource methods that change state (RFC 9110 "unsafe" methods).
 *
 * @SuppressWarnings("PHPMD.UnusedFormalParameter")
 */
final class UnsafeMethodMatcher extends AbstractMatcher
{
    private const array METHODS = ['onPost', 'onPut', 'onPatch', 'onDelete'];

    /**
     * @param ReflectionClass<object> $class
     * @param array<array-key, mixed> $arguments
     */
    #[Override]
    public function matchesClass(ReflectionClass $class, array $arguments): bool
    {
        return false;
    }

    /** @param array<array-key, mixed> $arguments */
    #[Override]
    public function matchesMethod(ReflectionMethod $method, array $arguments): bool
    {
        return in_array($method->getName(), self::METHODS, true);
    }
}

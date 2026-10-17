<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use Aura\Session\Phpfunc;
use Override;

use function call_user_func_array;

use const PHP_SESSION_ACTIVE;

/**
 * Reports the session as active so that Aura.Session works on $_SESSION without session_start(),
 * which fails once PHPUnit has written to stdout.
 */
final class FakeSessionPhpfunc extends Phpfunc
{
    /** @param array<mixed> $args */
    #[Override]
    public function __call($func, $args)
    {
        return match ($func) {
            'session_status' => PHP_SESSION_ACTIVE,
            'session_start' => true,
            default => call_user_func_array($func, $args),
        };
    }
}

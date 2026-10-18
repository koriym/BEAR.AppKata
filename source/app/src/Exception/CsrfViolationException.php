<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Exception;

use BEAR\Resource\Exception\BadRequestException;

/** Thrown before the resource method runs when a state-changing page request carries no valid CSRF token. */
final class CsrfViolationException extends BadRequestException
{
}

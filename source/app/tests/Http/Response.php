<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Http;

use function preg_match;

final readonly class Response
{
    public function __construct(
        public int $code,
        public string $headers,
        public string $body,
    ) {
    }

    public function header(string $name): string|null
    {
        return preg_match('/^' . $name . ':\s*(.+?)\r?$/mi', $this->headers, $m) === 1 ? $m[1] : null;
    }

    /** The value of a hidden input in the body, as a browser would submit it. */
    public function hiddenField(string $name): string|null
    {
        return preg_match('/name="' . $name . '" value="([^"]*)"/', $this->body, $m) === 1 ? $m[1] : null;
    }
}

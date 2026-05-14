<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Http;

use MyVendor\MyProject\Bootstrap;
use PHPUnit\Framework\TestCase;

use function json_decode;
use function ob_get_clean;
use function ob_start;

class WorkflowTest extends TestCase
{
    public function testIndex(): void
    {
        ob_start();
        $code = (new Bootstrap())(
            'app',
            ['_GET' => ['name' => 'BEAR.Sunday'], '_POST' => []],
            ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/index?name=BEAR.Sunday'],
        );
        $body = (string) ob_get_clean();

        $this->assertSame(0, $code);
        $this->assertSame(
            ['greeting' => 'Hello BEAR.Sunday (page)'],
            json_decode($body, true),
        );
    }
}

<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Http;

use RuntimeException;

use function explode;
use function fclose;
use function fsockopen;
use function is_resource;
use function microtime;
use function proc_open;
use function proc_terminate;
use function sprintf;
use function usleep;

use const PHP_BINARY;

/**
 * PHP's built-in server for HTTP tests.
 *
 * Server output goes to a log file instead of a pipe: a pipe nobody drains fills up
 * after enough requests (or one request under Xdebug) and the server blocks.
 */
final class BuiltinServer
{
    /** @var resource */
    private $process;

    public function __construct(string $host, string $index, string $log)
    {
        $process = proc_open(
            [PHP_BINARY, '-d', 'xdebug.mode=off', '-S', $host, $index],
            [['pipe', 'r'], ['file', $log, 'a'], ['file', $log, 'a']],
            $pipes,
        );
        if (! is_resource($process)) {
            throw new RuntimeException('Failed to start the built-in server'); // @codeCoverageIgnore
        }

        $this->process = $process;
        $this->waitUntilListening($host);
    }

    public function stop(): void
    {
        proc_terminate($this->process);
    }

    private function waitUntilListening(string $host): void
    {
        [$address, $port] = explode(':', $host);
        $deadline = microtime(true) + 5.0;
        do {
            $socket = @fsockopen($address, (int) $port, $errno, $errstr, 0.2);
            if (is_resource($socket)) {
                fclose($socket);

                return;
            }

            usleep(50_000);
        } while (microtime(true) < $deadline);

        throw new RuntimeException(sprintf('Built-in server did not start on %s', $host)); // @codeCoverageIgnore
    }
}

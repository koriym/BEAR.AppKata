<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Http;

use CurlHandle;
use RuntimeException;

use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt_array;
use function http_build_query;
use function is_string;
use function sprintf;
use function substr;

use const CURLINFO_HEADER_SIZE;
use const CURLINFO_RESPONSE_CODE;
use const CURLOPT_COOKIEFILE;
use const CURLOPT_HEADER;
use const CURLOPT_HTTPGET;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;

/**
 * One browsing session against the built-in server: cookies persist across requests,
 * forms are posted as a browser would (application/x-www-form-urlencoded).
 */
final class Browser
{
    private readonly CurlHandle $curl;

    public function __construct(private readonly string $baseUri)
    {
        $curl = curl_init();
        if ($curl === false) {
            throw new RuntimeException('curl_init() failed'); // @codeCoverageIgnore
        }

        // An empty cookie file enables the in-memory cookie engine.
        curl_setopt_array($curl, [
            CURLOPT_COOKIEFILE => '',
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $this->curl = $curl;
    }

    public function get(string $path): Response
    {
        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->baseUri . $path,
            CURLOPT_HTTPGET => true,
        ]);

        return $this->send();
    }

    /** @param array<string, string> $fields */
    public function post(string $path, array $fields): Response
    {
        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->baseUri . $path,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($fields),
        ]);

        return $this->send();
    }

    private function send(): Response
    {
        $raw = curl_exec($this->curl);
        if (! is_string($raw)) {
            throw new RuntimeException(sprintf('Request to %s failed', (string) curl_getinfo($this->curl, CURLOPT_URL))); // @codeCoverageIgnore
        }

        $headerSize = (int) curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);

        return new Response(
            (int) curl_getinfo($this->curl, CURLINFO_RESPONSE_CODE),
            substr($raw, 0, $headerSize),
            substr($raw, $headerSize),
        );
    }
}

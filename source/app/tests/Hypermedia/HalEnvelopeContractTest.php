<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Hypermedia;

use BEAR\Resource\ResourceInterface;
use JsonException;
use MyVendor\MyProject\Fake\AdminReadFakeModule;
use MyVendor\MyProject\Injector;
use PHPUnit\Framework\TestCase;

use function json_decode;

use const JSON_THROW_ON_ERROR;

final class HalEnvelopeContractTest extends TestCase
{
    /** @throws JsonException */
    public function testAdminIndexHalEnvelopeExposesProfileTransition(): void
    {
        $payload = $this->decode((string) $this->resource()->get('app://self/admin/index'));

        $this->assertSame('Admin', $payload['HELLO']);
        $this->assertHalLink($payload, 'self', '/admin/index');
        $this->assertHalLink($payload, 'goAdminProfile', 'app://self/admin/profile');
        $this->assertArrayNotHasKey('_embedded', $payload);
    }

    /** @throws JsonException */
    public function testAdminProfileHalEnvelopeExposesBackTransition(): void
    {
        $payload = $this->decode((string) $this->resource()->get('app://self/admin/profile', ['id' => 1]));

        $this->assertSame(1, $payload['id']);
        $this->assertSame('primary@example.com', $payload['primaryEmail']);
        $this->assertHalLink($payload, 'self', '/admin/profile?id=1');
        $this->assertHalLink($payload, 'goAdminIndex', 'app://self/admin/index');
        $this->assertArrayNotHasKey('_embedded', $payload);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    private function decode(string $json): array
    {
        $payload = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        $this->assertIsArray($payload);

        return $payload;
    }

    /** @param array<string, mixed> $payload */
    private function assertHalLink(array $payload, string $rel, string $href): void
    {
        $this->assertArrayHasKey('_links', $payload);
        $this->assertIsArray($payload['_links']);
        $this->assertArrayHasKey($rel, $payload['_links']);
        $this->assertIsArray($payload['_links'][$rel]);
        $this->assertSame($href, $payload['_links'][$rel]['href']);
    }

    private function resource(): ResourceInterface
    {
        $injector = Injector::getOverrideInstance('hal-api-app', new AdminReadFakeModule());

        return $injector->getInstance(ResourceInterface::class);
    }
}

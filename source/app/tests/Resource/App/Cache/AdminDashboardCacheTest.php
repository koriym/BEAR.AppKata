<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\App\Cache;

use BEAR\QueryRepository\Header;
use BEAR\Resource\ResourceInterface;
use BEAR\Sunday\Extension\Transfer\HttpCacheInterface;
use MyVendor\MyProject\Injector;
use MyVendor\MyProject\Module\CacheShowcaseModule;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function file_get_contents;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class AdminDashboardCacheTest extends TestCase
{
    private ResourceInterface $resource;
    private HttpCacheInterface $httpCache;

    protected function setUp(): void
    {
        $injector = Injector::getOverrideInstance('hal-api-app', new CacheShowcaseModule());
        $summaryStore = $injector->getInstance(AdminSummaryStore::class);
        $summaryStore->reset();
        $this->resource = $injector->getInstance(ResourceInterface::class);
        $this->httpCache = $injector->getInstance(HttpCacheInterface::class);
    }

    public function testCacheableResponseStoresEmbedDependencyAndEtag(): void
    {
        $ro = $this->resource->get('app://self/cache/admin-dashboard', ['id' => 1]);

        $this->assertSame(200, $ro->code);
        $this->assertArrayHasKey(Header::ETAG, $ro->headers);
        $this->assertArrayHasKey('summary', $ro->body);
        $payload = json_decode((string) $ro, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($payload);
        $this->assertArrayHasKey('summary', $payload);
        $this->assertSame([], $payload['summary']);
        $this->assertTrue($this->httpCache->isNotModified([
            Header::HTTP_IF_NONE_MATCH => $ro->headers[Header::ETAG],
        ]));
    }

    public function testRepeatedDashboardGetKeepsEtag(): void
    {
        $first = $this->resource->get('app://self/cache/admin-dashboard', ['id' => 1]);
        $second = $this->resource->get('app://self/cache/admin-dashboard', ['id' => 1]);

        $this->assertSame($first->headers[Header::ETAG], $second->headers[Header::ETAG]);
        $this->assertSame((string) $first, (string) $second);
    }

    public function testSourceContainsNoManualCachePrimitives(): void
    {
        $path = (new ReflectionClass(AdminDashboard::class))->getFileName();
        $this->assertIsString($path);
        $src = file_get_contents($path);
        $this->assertIsString($src);

        foreach (['Header::SURROGATE_KEY', 'UriTagInterface', 'DonutRepositoryInterface', 'invalidateTags'] as $needle) {
            $this->assertStringNotContainsString($needle, $src);
        }
    }
}

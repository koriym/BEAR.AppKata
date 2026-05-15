<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\App\Cache;

use BEAR\QueryRepository\Header;
use BEAR\RepositoryModule\Annotation\Cacheable;
use BEAR\Resource\ResourceInterface;
use BEAR\Sunday\Extension\Transfer\HttpCacheInterface;
use MyVendor\MyProject\Injector;
use MyVendor\MyProject\Module\CacheShowcaseModule;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class AdminSummaryCacheTest extends TestCase
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

    public function testGetSetsEtag(): void
    {
        $ro = $this->resource->get('app://self/cache/admin-summary', ['id' => 1]);

        $this->assertSame(200, $ro->code);
        $this->assertArrayHasKey(Header::ETAG, $ro->headers);
        $this->assertArrayHasKey(Header::LAST_MODIFIED, $ro->headers);
    }

    public function testRepeatedGetIsCacheHit(): void
    {
        $first = $this->resource->get('app://self/cache/admin-summary', ['id' => 1]);
        $second = $this->resource->get('app://self/cache/admin-summary', ['id' => 1]);

        $this->assertSame($first->headers[Header::ETAG], $second->headers[Header::ETAG]);
        $this->assertSame((string) $first, (string) $second);
        $this->assertTrue($this->httpCache->isNotModified([
            Header::HTTP_IF_NONE_MATCH => $first->headers[Header::ETAG],
        ]));
    }

    public function testPutInvalidatesEtag(): void
    {
        $first = $this->resource->get('app://self/cache/admin-summary', ['id' => 1]);
        $oldEtag = $first->headers[Header::ETAG];

        $put = $this->resource->put('app://self/cache/admin-summary', [
            'id' => 1,
            'displayName' => 'Edited Admin',
        ]);
        $this->assertSame(200, $put->code);

        $this->assertFalse($this->httpCache->isNotModified([Header::HTTP_IF_NONE_MATCH => $oldEtag]));

        $second = $this->resource->get('app://self/cache/admin-summary', ['id' => 1]);
        $this->assertNotSame($oldEtag, $second->headers[Header::ETAG]);
        $this->assertStringContainsString('Edited Admin', (string) $second);
    }

    public function testRelyOnCacheableAttributeOnly(): void
    {
        $reflection = new ReflectionClass(AdminSummary::class);

        $this->assertCount(1, $reflection->getAttributes(Cacheable::class));
    }
}

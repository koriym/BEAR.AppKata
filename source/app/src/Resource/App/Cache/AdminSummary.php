<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\App\Cache;

use BEAR\ApiDoc\Annotation\Alps;
use BEAR\RepositoryModule\Annotation\Cacheable;
use BEAR\Resource\Annotation\JsonSchema;
use BEAR\Resource\Code;
use BEAR\Resource\ResourceObject;

/**
 * Cache showcase leaf: `#[Cacheable]` is the whole cache surface.
 */
#[Alps('CacheAdminSummary')]
#[Cacheable]
class AdminSummary extends ResourceObject
{
    public function __construct(
        private readonly AdminSummaryStore $store,
    ) {
    }

    #[Alps('goCacheAdminSummary')]
    #[JsonSchema('cache_admin_summary.json')]
    public function onGet(int $id = 1): static
    {
        $summary = $this->store->get($id);
        if ($summary === null) {
            $this->code = Code::NOT_FOUND;
            $this->body = ['message' => 'Admin summary not found', 'id' => $id];

            return $this;
        }

        $this->body = $summary;

        return $this;
    }

    #[Alps('doUpdateCacheAdminSummary')]
    #[JsonSchema(schema: 'write_response.json', params: 'cache_admin_summary_update.json')]
    public function onPut(int $id, string $displayName): static
    {
        if (! $this->store->update($id, $displayName)) {
            $this->code = Code::NOT_FOUND;
            $this->body = ['message' => 'Admin summary not found', 'id' => $id];

            return $this;
        }

        $this->body = ['id' => $id];

        return $this;
    }
}

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
    /** @var array<int, array{id: int, displayName: string, revision: int}> */
    private static array $summaries = [
        1 => ['id' => 1, 'displayName' => 'Admin One', 'revision' => 1],
    ];

    public static function reset(): void
    {
        self::$summaries = [
            1 => ['id' => 1, 'displayName' => 'Admin One', 'revision' => 1],
        ];
    }

    #[Alps('goCacheAdminSummary')]
    #[JsonSchema('cache_admin_summary.json')]
    public function onGet(int $id = 1): static
    {
        if (! isset(self::$summaries[$id])) {
            $this->code = Code::NOT_FOUND;
            $this->body = ['message' => 'Admin summary not found', 'id' => $id];

            return $this;
        }

        $this->body = self::$summaries[$id];

        return $this;
    }

    #[Alps('doUpdateCacheAdminSummary')]
    #[JsonSchema(schema: 'write_response.json', params: 'cache_admin_summary_update.json')]
    public function onPut(int $id, string $displayName): static
    {
        if (! isset(self::$summaries[$id])) {
            $this->code = Code::NOT_FOUND;
            $this->body = ['message' => 'Admin summary not found', 'id' => $id];

            return $this;
        }

        self::$summaries[$id] = [
            'id' => $id,
            'displayName' => $displayName,
            'revision' => self::$summaries[$id]['revision'] + 1,
        ];
        $this->body = ['id' => $id];

        return $this;
    }
}

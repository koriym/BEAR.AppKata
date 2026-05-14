<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\App\Cache;

use BEAR\ApiDoc\Annotation\Alps;
use BEAR\RepositoryModule\Annotation\CacheableResponse;
use BEAR\Resource\Annotation\Embed;
use BEAR\Resource\Annotation\JsonSchema;
use BEAR\Resource\ResourceObject;

/**
 * Cache showcase parent: `#[CacheableResponse]` tracks the embedded child.
 */
#[Alps('CacheAdminDashboard')]
#[CacheableResponse]
class AdminDashboard extends ResourceObject
{
    #[Alps('goCacheAdminDashboard')]
    #[Embed(rel: 'summary', src: 'app://self/cache/admin-summary')]
    #[JsonSchema('cache_admin_dashboard.json')]
    public function onGet(int $id = 1): static
    {
        $this->body['summary']->addQuery(['id' => $id]);
        $this->body += [
            'id' => $id,
            'dependencyUri' => 'app://self/cache/admin-summary?id=' . $id,
            'cachePattern' => 'CacheableResponse + Embed',
        ];

        return $this;
    }
}

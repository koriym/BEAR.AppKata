<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\App\Admin;

use BEAR\ApiDoc\Annotation\Alps;
use BEAR\Resource\Annotation\JsonSchema;
use BEAR\Resource\Annotation\Link;
use BEAR\Resource\ResourceObject;
use Koriym\HttpConstants\CacheControl;
use Koriym\HttpConstants\ResponseHeader;

#[Alps('AdminIndex')]
class Index extends ResourceObject
{
    /** @var array<string, string> */
    public $headers = [ResponseHeader::CACHE_CONTROL => CacheControl::PUBLIC_ . ',max-age=86400'];

    /** @var array{HELLO: string, defaultAdminId: int} */
    public $body;

    #[Alps('goAdminIndex')]
    #[Link(rel: 'goAdminProfile', href: 'app://self/admin/profile{?id}')]
    #[JsonSchema('admin_index.json')]
    public function onGet(): static
    {
        $this->body = [
            'HELLO' => 'Admin',
            'defaultAdminId' => 1,
        ];

        return $this;
    }
}

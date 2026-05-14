<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Hypermedia;

use BEAR\Resource\Annotation\Link;
use BEAR\Resource\ResourceObject;
use MyVendor\MyProject\Fake\FakeAdminEmailQuery;
use MyVendor\MyProject\Fake\FakeAdminPermissionQuery;
use MyVendor\MyProject\Fake\FakeAdminQuery;
use MyVendor\MyProject\Resource\App\Admin\Index;
use MyVendor\MyProject\Resource\App\Admin\Profile;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

abstract class AbstractWorkflowTestCase extends TestCase
{
    protected Index $index;
    protected Profile $profile;

    protected function setUp(): void
    {
        $this->index = new Index();
        $this->profile = new Profile(
            new FakeAdminQuery(),
            new FakeAdminEmailQuery(),
            new FakeAdminPermissionQuery(),
        );
    }

    /** @param array<string, mixed> $vars */
    protected function follow(ResourceObject $ro, string $rel, array $vars = []): ResourceObject
    {
        $this->assertRelExists($ro, $rel);
        $next = match ($rel) {
            'goAdminProfile' => $this->profile->onGet((int) $vars['id']),
            'goAdminIndex' => $this->index->onGet(),
            default => self::fail("Unknown rel `{$rel}`"),
        };
        $this->assertSame(200, $next->code, "Following rel `{$rel}` should return 200");

        return $next;
    }

    protected function assertRelExists(ResourceObject $ro, string $rel): void
    {
        $method = new ReflectionMethod($ro::class, 'onGet');
        foreach ($method->getAttributes(Link::class) as $attribute) {
            if ($attribute->newInstance()->rel === $rel) {
                $this->addToAssertionCount(1);

                return;
            }
        }

        self::fail($ro::class . " does not declare rel `{$rel}`");
    }
}

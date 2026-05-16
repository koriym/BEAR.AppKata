<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Hypermedia;

use AppCore\Infrastructure\Query\AdminEmailQueryInterface;
use AppCore\Infrastructure\Query\AdminPermissionQueryInterface;
use AppCore\Infrastructure\Query\AdminQueryInterface;
use BEAR\Resource\Annotation\Link;
use BEAR\Resource\ResourceObject;
use MyVendor\MyProject\Fake\AdminReadFakeModule;
use MyVendor\MyProject\Resource\App\Admin\Index;
use MyVendor\MyProject\Resource\App\Admin\Profile;
use PHPUnit\Framework\TestCase;
use Ray\Di\Injector as DiInjector;
use ReflectionMethod;

use function dirname;

abstract class AbstractWorkflowTestCase extends TestCase
{
    protected Index $index;
    protected Profile $profile;

    protected function setUp(): void
    {
        $this->index = new Index();
        $this->profile = $this->profileFromFixtures();
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

    private function profileFromFixtures(): Profile
    {
        $injector = new DiInjector(
            new AdminReadFakeModule(),
            dirname(__DIR__, 2) . '/var/tmp/fake-query-admin-read-hypermedia',
        );

        $admin = $injector->getInstance(AdminQueryInterface::class);
        $emails = $injector->getInstance(AdminEmailQueryInterface::class);
        $permissions = $injector->getInstance(AdminPermissionQueryInterface::class);

        return new Profile($admin, $emails, $permissions);
    }
}

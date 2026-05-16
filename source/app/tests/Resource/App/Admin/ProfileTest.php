<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\App\Admin;

use AppCore\Infrastructure\Query\AdminEmailQueryInterface;
use AppCore\Infrastructure\Query\AdminPermissionQueryInterface;
use AppCore\Infrastructure\Query\AdminQueryInterface;
use MyVendor\MyProject\Fake\AdminReadFakeModule;
use PHPUnit\Framework\TestCase;
use Ray\Di\Injector as DiInjector;

use function array_key_exists;
use function dirname;
use function md5;

final class ProfileTest extends TestCase
{
    private Profile $profile;

    protected function setUp(): void
    {
        $this->profile = $this->profileFromFixtures(__DIR__ . '/../../../fixtures/admin-read');
    }

    public function testOnGetReturnsAdminReadContract(): void
    {
        $ro = $this->profile->onGet(1);

        $this->assertSame(200, $ro->code);
        $this->assertSame(1, $ro->body['id']);
        $this->assertSame('admin', $ro->body['username']);
        $this->assertSame('primary@example.com', $ro->body['primaryEmail']);
        $this->assertSame(1, $ro->body['verifiedEmailCount']);
        $this->assertSame(['admin'], $ro->body['allowedResources']);
        $this->assertFalse(array_key_exists('password', $ro->body));
    }

    public function testOnGetMissingReturns404(): void
    {
        $ro = $this->profileFromFixtures(__DIR__ . '/../../../fixtures/admin-read-empty')->onGet(999);

        $this->assertSame(404, $ro->code);
        $this->assertSame('Admin not found', $ro->body['message']);
        $this->assertSame(999, $ro->body['id']);
    }

    private function profileFromFixtures(string $fakeDir): Profile
    {
        $injector = new DiInjector(
            new AdminReadFakeModule($fakeDir),
            dirname(__DIR__, 4) . '/var/tmp/fake-query-admin-read-' . md5($fakeDir),
        );

        $admin = $injector->getInstance(AdminQueryInterface::class);
        $emails = $injector->getInstance(AdminEmailQueryInterface::class);
        $permissions = $injector->getInstance(AdminPermissionQueryInterface::class);

        return new Profile($admin, $emails, $permissions);
    }
}

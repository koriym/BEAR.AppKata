<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use AppCore\Infrastructure\Entity\AdminEmailEntity;
use AppCore\Infrastructure\Entity\AdminEntity;
use AppCore\Infrastructure\Entity\AdminPermissionEntity;
use AppCore\Infrastructure\Query\AdminEmailQueryInterface;
use AppCore\Infrastructure\Query\AdminPermissionQueryInterface;
use AppCore\Infrastructure\Query\AdminQueryInterface;
use AppCore\Infrastructure\Query\AdminSelectionQueryInterface;
use AppCore\Infrastructure\Result\AdminSelection;
use PHPUnit\Framework\TestCase;
use Ray\Di\Injector as DiInjector;

use function dirname;
use function md5;

final class FakeQueryAdminReadTest extends TestCase
{
    public function testAdminReadFixturesHydrateEntitiesThroughDbQueryFactories(): void
    {
        $injector = $this->injector(__DIR__ . '/../fixtures/admin-read');

        /** @var AdminQueryInterface $adminQuery */
        $adminQuery = $injector->getInstance(AdminQueryInterface::class);
        $admin = $adminQuery->item(1);
        $this->assertInstanceOf(AdminEntity::class, $admin);
        $this->assertSame('admin', $admin->username);
        $this->assertSame('Admin User', $admin->displayName);
        $this->assertSame('2026-01-01 00:00:00', $admin->createdDate->format('Y-m-d H:i:s'));

        /** @var AdminEmailQueryInterface $emailQuery */
        $emailQuery = $injector->getInstance(AdminEmailQueryInterface::class);
        $emails = $emailQuery->list(1);
        $this->assertContainsOnlyInstancesOf(AdminEmailEntity::class, $emails);
        $this->assertSame(['backup@example.com', 'primary@example.com'], [
            $emails[0]->emailAddress,
            $emails[1]->emailAddress,
        ]);
        $this->assertNull($emails[0]->verifiedDate);
        $this->assertSame('2026-01-02 00:00:00', $emails[1]->verifiedDate?->format('Y-m-d H:i:s'));

        /** @var AdminPermissionQueryInterface $permissionQuery */
        $permissionQuery = $injector->getInstance(AdminPermissionQueryInterface::class);
        $permissions = $permissionQuery->list(1);
        $this->assertContainsOnlyInstancesOf(AdminPermissionEntity::class, $permissions);
        $this->assertSame(['allow', 'allow', 'deny'], [
            $permissions[0]->access,
            $permissions[1]->access,
            $permissions[2]->access,
        ]);
    }

    public function testAdminSelectionUsesConstructorBasedPostQueryWrapper(): void
    {
        $injector = $this->injector(__DIR__ . '/../fixtures/admin-read');

        /** @var AdminSelectionQueryInterface $query */
        $query = $injector->getInstance(AdminSelectionQueryInterface::class);
        $selection = $query->list();

        $this->assertInstanceOf(AdminSelection::class, $selection);
        $this->assertSame(['admin', 'disabled'], $selection->usernames());
        $this->assertSame(['admin'], $selection->active()->usernames());
        $this->assertCount(2, $selection);
    }

    public function testMissingNullableRowFixtureReturnsNull(): void
    {
        $injector = $this->injector(__DIR__ . '/../fixtures/admin-read-empty');

        /** @var AdminQueryInterface $query */
        $query = $injector->getInstance(AdminQueryInterface::class);

        $this->assertNull($query->item(999));
    }

    private function injector(string $fakeDir): DiInjector
    {
        return new DiInjector(
            new AdminReadFakeModule($fakeDir),
            dirname(__DIR__, 2) . '/var/tmp/fake-query-admin-read-' . md5($fakeDir),
        );
    }
}

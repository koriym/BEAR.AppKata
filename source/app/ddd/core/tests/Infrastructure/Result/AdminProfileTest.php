<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Result;

use AppCore\Domain\AccessControl\Access;
use AppCore\Domain\AccessControl\Permission;
use AppCore\Infrastructure\Entity\AdminEmailEntity;
use AppCore\Infrastructure\Entity\AdminEntity;
use AppCore\Infrastructure\Entity\AdminPermissionEntity;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class AdminProfileTest extends TestCase
{
    public function testPureInquiryMethods(): void
    {
        $profile = new AdminProfile(
            new AdminEntity(
                1,
                'admin',
                'secret-hash',
                'Admin User',
                1,
                new DateTimeImmutable('2026-01-01 00:00:00'),
                new DateTimeImmutable('2026-01-02 00:00:00'),
            ),
            [
                new AdminEmailEntity(1, 1, 'backup@example.com', null, new DateTimeImmutable(), new DateTimeImmutable()),
                new AdminEmailEntity(2, 1, 'primary@example.com', new DateTimeImmutable(), new DateTimeImmutable(), new DateTimeImmutable()),
            ],
            [
                new AdminPermissionEntity(1, 1, Access::Allow->value, 'admin', Permission::Read->value, new DateTimeImmutable()),
                new AdminPermissionEntity(2, 1, Access::Allow->value, 'settings', Permission::Write->value, new DateTimeImmutable()),
                new AdminPermissionEntity(3, 1, Access::Deny->value, 'admin', Permission::Privilege->value, new DateTimeImmutable()),
                new AdminPermissionEntity(4, 1, Access::Allow->value, 'broken', 'unknown', new DateTimeImmutable()),
            ],
        );

        $this->assertSame(1, $profile->adminId());
        $this->assertSame(['username' => 'admin', 'displayName' => 'Admin User', 'active' => true], $profile->account());
        $this->assertSame('primary@example.com', $profile->primaryEmailAddress());
        $this->assertCount(1, $profile->verifiedEmails());
        $this->assertSame(['admin', 'settings'], $profile->allowedResourceNames());
        $this->assertSame(['read', 'write'], $profile->allowedPermissionNames());
    }
}

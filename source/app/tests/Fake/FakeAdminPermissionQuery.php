<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use AppCore\Domain\AccessControl\Access;
use AppCore\Domain\AccessControl\Permission;
use AppCore\Infrastructure\Entity\AdminPermissionEntity;
use AppCore\Infrastructure\Query\AdminPermissionQueryInterface;
use DateTimeImmutable;

final class FakeAdminPermissionQuery implements AdminPermissionQueryInterface
{
    /** @return list<AdminPermissionEntity> */
    public function list(int $adminId): array
    {
        if ($adminId !== 1) {
            return [];
        }

        return [
            new AdminPermissionEntity(
                1,
                1,
                Access::Allow->value,
                'admin',
                Permission::Read->value,
                new DateTimeImmutable('2026-01-01 00:00:00'),
            ),
            new AdminPermissionEntity(
                2,
                1,
                Access::Allow->value,
                'admin',
                Permission::Write->value,
                new DateTimeImmutable('2026-01-01 00:00:00'),
            ),
            new AdminPermissionEntity(
                3,
                1,
                Access::Deny->value,
                'admin',
                Permission::Privilege->value,
                new DateTimeImmutable('2026-01-01 00:00:00'),
            ),
        ];
    }
}

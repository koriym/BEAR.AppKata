<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Entity;

use DateTimeImmutable;

class AdminPermissionEntity
{
    public function __construct(
        public readonly int $id,
        public readonly int $adminId,
        public readonly string $access,
        public readonly string $resourceName,
        public readonly string $permissionName,
        public readonly DateTimeImmutable $createdDate,
    ) {
    }
}

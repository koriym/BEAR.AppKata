<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Entity;

use DateTimeImmutable;

class AdminTokenEntity
{
    public function __construct(
        public readonly int $id,
        public readonly int $adminId,
        public readonly string $token,
        public readonly DateTimeImmutable $expireDate,
        public readonly DateTimeImmutable $createdDate,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Entity;

use DateTimeImmutable;

class AdminEntity
{
    /** @param positive-int $id */
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $password,
        public readonly string $displayName,
        public readonly int $active,
        public readonly DateTimeImmutable $createdDate,
        public readonly DateTimeImmutable $updatedDate,
    ) {
    }
}

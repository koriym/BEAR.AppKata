<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Entity;

use DateTimeImmutable;

class ThrottleEntity
{
    public function __construct(
        public readonly int $id,
        public readonly string $throttleKey,
        public readonly string $remoteIp,
        public readonly int $iterationCount,
        public readonly int $maxAttempts,
        public readonly string $interval,
        public readonly DateTimeImmutable $expireDate,
        public readonly DateTimeImmutable $createdDate,
        public readonly DateTimeImmutable $updatedDate,
    ) {
    }
}

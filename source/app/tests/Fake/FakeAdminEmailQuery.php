<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use AppCore\Infrastructure\Entity\AdminEmailEntity;
use AppCore\Infrastructure\Query\AdminEmailQueryInterface;
use DateTimeImmutable;

final class FakeAdminEmailQuery implements AdminEmailQueryInterface
{
    /** @return list<AdminEmailEntity> */
    public function list(int $adminId): array
    {
        if ($adminId !== 1) {
            return [];
        }

        return [
            new AdminEmailEntity(
                1,
                1,
                'backup@example.com',
                null,
                new DateTimeImmutable('2026-01-01 00:00:00'),
                new DateTimeImmutable('2026-01-01 00:00:00'),
            ),
            new AdminEmailEntity(
                2,
                1,
                'primary@example.com',
                new DateTimeImmutable('2026-01-02 00:00:00'),
                new DateTimeImmutable('2026-01-01 00:00:00'),
                new DateTimeImmutable('2026-01-02 00:00:00'),
            ),
        ];
    }
}

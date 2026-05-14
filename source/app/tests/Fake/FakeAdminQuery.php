<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use AppCore\Infrastructure\Entity\AdminEntity;
use AppCore\Infrastructure\Query\AdminQueryInterface;
use DateTimeImmutable;

final class FakeAdminQuery implements AdminQueryInterface
{
    public function item(int $id): AdminEntity|null
    {
        if ($id !== 1) {
            return null;
        }

        return self::admin();
    }

    public function itemByUsername(string $username): AdminEntity|null
    {
        return $username === 'admin' ? self::admin() : null;
    }

    public function itemByEmailAddress(string $emailAddress): AdminEntity|null
    {
        return $emailAddress === 'primary@example.com' ? self::admin() : null;
    }

    public static function admin(): AdminEntity
    {
        return new AdminEntity(
            1,
            'admin',
            'hashed-password-never-exposed',
            'Admin User',
            1,
            new DateTimeImmutable('2026-01-01 00:00:00'),
            new DateTimeImmutable('2026-01-02 00:00:00'),
        );
    }
}

<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Result;

use AppCore\Infrastructure\Entity\AdminEntity;
use Aura\Sql\ExtendedPdoInterface;
use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\TestCase;
use Ray\MediaQuery\Result\PostQueryContext;

use function iterator_to_array;

final class AdminSelectionTest extends TestCase
{
    public function testSelectResultWrapsHydratedRows(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $statement = $pdo->query('SELECT 1');
        $this->assertNotFalse($statement);

        $active = new AdminEntity(1, 'active', 'hash', 'Active', 1, new DateTimeImmutable(), new DateTimeImmutable());
        $inactive = new AdminEntity(2, 'inactive', 'hash', 'Inactive', 0, new DateTimeImmutable(), new DateTimeImmutable());
        $selection = AdminSelection::fromContext(new PostQueryContext(
            $statement,
            $this->createStub(ExtendedPdoInterface::class),
            [],
            [$active, $inactive],
        ));

        $this->assertSame(2, $selection->count());
        $this->assertSame(['active', 'inactive'], $selection->usernames());
        $this->assertSame(['active'], $selection->active()->usernames());
        $this->assertSame($active, $selection->first());
        $this->assertContainsOnlyInstancesOf(AdminEntity::class, iterator_to_array($selection));
    }
}

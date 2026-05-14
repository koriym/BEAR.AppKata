<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Smoke;

use AppCore\Infrastructure\Entity\AdminEntity;
use AppCore\Infrastructure\Query\AdminSelectionQueryInterface;
use AppCore\Infrastructure\Query\Samples\AdminAffectedRowsCommandInterface;
use AppCore\Infrastructure\Result\AdminSelection;
use Aura\Sql\ExtendedPdoInterface;
use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\TestCase;
use Ray\MediaQuery\Result\AffectedRows;
use Ray\MediaQuery\Result\PostQueryContext;
use ReflectionMethod;

use function iterator_to_array;

final class MediaQuerySamplesTest extends TestCase
{
    public function testSelectResultClassWrapsHydratedRows(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $statement = $pdo->query('SELECT 1');
        $this->assertNotFalse($statement);

        $selection = AdminSelection::fromContext(new PostQueryContext(
            $statement,
            $this->createStub(ExtendedPdoInterface::class),
            [],
            [
                new AdminEntity(1, 'admin', 'hash', 'Admin', 1, new DateTimeImmutable(), new DateTimeImmutable()),
                new AdminEntity(2, 'disabled', 'hash', 'Disabled', 0, new DateTimeImmutable(), new DateTimeImmutable()),
            ],
        ));

        $this->assertSame(['admin', 'disabled'], $selection->usernames());
        $this->assertSame(['admin'], $selection->active()->usernames());
        $this->assertContainsOnlyInstancesOf(AdminEntity::class, iterator_to_array($selection));
    }

    public function testAffectedRowsResultMetadata(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE admins (id INTEGER PRIMARY KEY, username TEXT)');
        $pdo->exec("INSERT INTO admins (id, username) VALUES (1, 'admin')");
        $statement = $pdo->prepare('UPDATE admins SET username = :username WHERE id = :id');
        $this->assertNotFalse($statement);
        $statement->execute(['username' => 'updated', 'id' => 1]);

        $affected = AffectedRows::fromContext(new PostQueryContext(
            $statement,
            $this->createStub(ExtendedPdoInterface::class),
            ['username' => 'updated', 'id' => 1],
        ));

        $this->assertSame(1, $affected->count);
        $this->assertTrue($affected->isAffected());
    }

    public function testSampleInterfacesDeclareBdrResultTypes(): void
    {
        $selection = new ReflectionMethod(AdminSelectionQueryInterface::class, 'list');
        $update = new ReflectionMethod(AdminAffectedRowsCommandInterface::class, 'update');
        $delete = new ReflectionMethod(AdminAffectedRowsCommandInterface::class, 'delete');

        $this->assertSame(AdminSelection::class, (string) $selection->getReturnType());
        $this->assertSame(AffectedRows::class, (string) $update->getReturnType());
        $this->assertSame(AffectedRows::class, (string) $delete->getReturnType());
    }
}

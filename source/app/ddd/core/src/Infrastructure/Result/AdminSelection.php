<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Result;

use AppCore\Infrastructure\Entity\AdminEntity;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Override;
use Ray\MediaQuery\Result\PostQueryContext;
use Ray\MediaQuery\Result\PostQueryInterface;

use function array_filter;
use function array_map;
use function array_values;
use function count;

/**
 * SELECT result wrapper for Ray.MediaQuery's PostQueryInterface path.
 *
 * @implements IteratorAggregate<int, AdminEntity>
 */
final readonly class AdminSelection implements PostQueryInterface, IteratorAggregate, Countable
{
    /** @param list<AdminEntity> $rows */
    public function __construct(
        public array $rows,
    ) {
    }

    #[Override]
    public static function fromContext(PostQueryContext $context): static
    {
        /** @var list<AdminEntity> $rows */
        $rows = $context->rows;

        return new self($rows);
    }

    public function active(): self
    {
        return new self(array_values(array_filter(
            $this->rows,
            static fn (AdminEntity $admin): bool => $admin->active === 1,
        )));
    }

    /** @return list<string> */
    public function usernames(): array
    {
        return array_map(static fn (AdminEntity $admin): string => $admin->username, $this->rows);
    }

    public function first(): AdminEntity|null
    {
        return $this->rows[0] ?? null;
    }

    public function count(): int
    {
        return count($this->rows);
    }

    /** @return ArrayIterator<int, AdminEntity> */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->rows);
    }
}

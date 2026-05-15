<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\App\Cache;

final class AdminSummaryStore
{
    /** @var array<int, array{id: int, displayName: string, revision: int}> */
    private array $summaries;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->summaries = [
            1 => ['id' => 1, 'displayName' => 'Admin One', 'revision' => 1],
        ];
    }

    /** @return array{id: int, displayName: string, revision: int}|null */
    public function get(int $id): array|null
    {
        return $this->summaries[$id] ?? null;
    }

    public function update(int $id, string $displayName): bool
    {
        $summary = $this->summaries[$id] ?? null;
        if ($summary === null) {
            return false;
        }

        $this->summaries[$id] = [
            'id' => $id,
            'displayName' => $displayName,
            'revision' => $summary['revision'] + 1,
        ];

        return true;
    }
}

<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Query\Samples;

use DateTimeInterface;
use Ray\MediaQuery\Annotation\DbQuery;
use Ray\MediaQuery\Result\AffectedRows;

interface AdminAffectedRowsCommandInterface
{
    #[DbQuery('admins/admin_update')]
    public function update(
        int $id,
        string $username,
        string $displayName,
        int $active,
        DateTimeInterface|null $updatedDate = null,
    ): AffectedRows;

    #[DbQuery('admins/admin_delete_delete')]
    public function delete(int $adminId, DateTimeInterface $deletedDate): AffectedRows;
}

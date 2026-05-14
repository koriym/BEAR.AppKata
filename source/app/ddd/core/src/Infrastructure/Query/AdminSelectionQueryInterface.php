<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Query;

use AppCore\Infrastructure\Entity\AdminEntityFactory;
use AppCore\Infrastructure\Result\AdminSelection;
use Ray\MediaQuery\Annotation\DbQuery;

interface AdminSelectionQueryInterface
{
    #[DbQuery('admins/admin_selection_list', factory: AdminEntityFactory::class)]
    public function list(int|null $active = null): AdminSelection;
}

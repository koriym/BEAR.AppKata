<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use AppCore\Infrastructure\Query\AdminEmailQueryInterface;
use AppCore\Infrastructure\Query\AdminPermissionQueryInterface;
use AppCore\Infrastructure\Query\AdminQueryInterface;
use Override;
use Ray\Di\AbstractModule;

final class AdminReadFakeModule extends AbstractModule
{
    #[Override]
    protected function configure(): void
    {
        $this->bind(AdminQueryInterface::class)->to(FakeAdminQuery::class);
        $this->bind(AdminEmailQueryInterface::class)->to(FakeAdminEmailQuery::class);
        $this->bind(AdminPermissionQueryInterface::class)->to(FakeAdminPermissionQuery::class);
    }
}

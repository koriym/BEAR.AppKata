<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use AppCore\Infrastructure\Query\AdminEmailQueryInterface;
use AppCore\Infrastructure\Query\AdminPermissionQueryInterface;
use AppCore\Infrastructure\Query\AdminQueryInterface;
use AppCore\Infrastructure\Query\AdminSelectionQueryInterface;
use Override;
use Ray\Di\AbstractModule;
use Ray\Di\Injector as DiInjector;
use Ray\FakeQuery\FakeQueryModule;

use function md5;

final class AdminReadFakeModule extends AbstractModule
{
    public function __construct(
        private readonly string $fakeDir = __DIR__ . '/../fixtures/admin-read',
        AbstractModule|null $module = null,
    ) {
        parent::__construct($module);
    }

    #[Override]
    protected function configure(): void
    {
        $interfaceDir = __DIR__ . '/../../ddd/core/src/Infrastructure/Query';
        $injector = new DiInjector(
            new class ($this->fakeDir, $interfaceDir) extends AbstractModule {
                public function __construct(
                    private readonly string $fakeDir,
                    private readonly string $interfaceDir,
                ) {
                    parent::__construct();
                }

                #[Override]
                protected function configure(): void
                {
                    $this->install(new FakeQueryModule($this->fakeDir, $this->interfaceDir));
                }
            },
            __DIR__ . '/../../var/tmp/ray-fake-query-' . md5($this->fakeDir),
        );

        /** @var AdminQueryInterface $admin */
        $admin = $injector->getInstance(AdminQueryInterface::class);
        /** @var AdminEmailQueryInterface $emails */
        $emails = $injector->getInstance(AdminEmailQueryInterface::class);
        /** @var AdminPermissionQueryInterface $permissions */
        $permissions = $injector->getInstance(AdminPermissionQueryInterface::class);
        /** @var AdminSelectionQueryInterface $selection */
        $selection = $injector->getInstance(AdminSelectionQueryInterface::class);

        $this->bind(AdminQueryInterface::class)->toInstance($admin);
        $this->bind(AdminEmailQueryInterface::class)->toInstance($emails);
        $this->bind(AdminPermissionQueryInterface::class)->toInstance($permissions);
        $this->bind(AdminSelectionQueryInterface::class)->toInstance($selection);
    }
}

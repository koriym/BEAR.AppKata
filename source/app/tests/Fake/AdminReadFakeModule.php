<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Fake;

use Override;
use Ray\Di\AbstractModule;
use Ray\FakeQuery\FakeQueryModule;

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
        $this->install(new FakeQueryModule(
            $this->fakeDir,
            __DIR__ . '/../../ddd/core/src/Infrastructure/Query',
        ));
    }
}

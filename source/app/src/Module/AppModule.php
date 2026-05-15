<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Module;

use BEAR\Package\AbstractAppModule;
use BEAR\Package\PackageModule;
use BEAR\Package\Provide\Router\AuraRouterModule;
use BEAR\Resource\Module\JsonSchemaModule;
use BEAR\Sunday\Extension\Router\RouterInterface;
use Koriym\EnvJson\EnvJson;
use MyVendor\MyProject\Resource\App\Cache\AdminSummaryStore;
use MyVendor\MyProject\Router\CompatibleAuraRouter;
use Ray\AuraSqlModule\AuraSqlModule;
use Ray\Di\Scope;
use Ray\IdentityValueModule\IdentityValueModule;
use Ray\MediaQuery\DbQueryConfig;
use Ray\MediaQuery\MediaQueryModule;
use Ray\MediaQuery\Queries;

use function dirname;
use function getenv;

/** @SuppressWarnings("PHPMD.CouplingBetweenObjects") */
class AppModule extends AbstractAppModule
{
    /** @SuppressWarnings("PHPMD.StaticAccess") */
    protected function configure(): void
    {
        (new EnvJson())->load(dirname(__DIR__, 2));

        $appDir = $this->appMeta->appDir;

        $this->install(new AuraRouterModule($appDir . '/var/conf/aura.route.php'));
        $this->bind(RouterInterface::class)
            ->annotatedWith('primary_router')
            ->to(CompatibleAuraRouter::class)
            ->in(Scope::SINGLETON);

        $this->bind()->annotatedWith('pdo_dsn')->toInstance((string) getenv('DB_DSN'));
        $this->bind()->annotatedWith('pdo_username')->toInstance((string) getenv('DB_USER'));
        $this->bind()->annotatedWith('pdo_password')->toInstance((string) getenv('DB_PASS'));

        $this->install(
            new AuraSqlModule(
                (string) getenv('DB_DSN'),
                (string) getenv('DB_USER'),
                (string) getenv('DB_PASS'),
                (string) getenv('DB_SLAVE'),
            ),
        );

        $this->install(
            new MediaQueryModule(
                Queries::fromDir($this->appMeta->appDir . '/ddd/core/src/Infrastructure/Query'),
                [new DbQueryConfig($this->appMeta->appDir . '/var/sql')],
            ),
        );

        $this->install(new IdentityValueModule());

        $this->bind(AdminSummaryStore::class)->in(Scope::SINGLETON);

        $this->install(
            new JsonSchemaModule(
                $this->appMeta->appDir . '/var/schema/response',
                $this->appMeta->appDir . '/var/schema/request',
            ),
        );

        $this->install(
            new BaseModule(
                $this->appMeta->appDir . '/var/email',
                $this->appMeta->appDir . '/var/lang',
            ),
        );

        $this->install(new PackageModule());

        $this->install(new DefaultModule());
    }
}

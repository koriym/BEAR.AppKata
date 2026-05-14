<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Module;

use BEAR\RepositoryModule\Annotation\ResourceObjectPool;
use Override;
use Ray\Di\AbstractModule;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Enables an in-memory QueryRepository cache for hermetic showcase tests.
 */
final class CacheShowcaseModule extends AbstractModule
{
    #[Override]
    protected function configure(): void
    {
        $this->bind(AdapterInterface::class)->annotatedWith(ResourceObjectPool::class)->toInstance(new ArrayAdapter());
    }
}

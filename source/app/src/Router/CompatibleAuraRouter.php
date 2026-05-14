<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Router;

use Aura\Router\RouterContainer;
use BEAR\Package\Provide\Router\AuraRouter;
use BEAR\Package\Provide\Router\HttpMethodParamsInterface;
use BEAR\Package\Provide\Router\WebServerRequestHeaderProvider;
use BEAR\Sunday\Annotation\DefaultSchemeHost;

/**
 * Ray.Di 2.20 treats ProviderInterface parameters as provider-set injection.
 *
 * bear/aura-router-module 2.4 still asks for ProviderInterface directly, so the
 * application binds this compatible constructor while keeping Aura routing.
 */
final class CompatibleAuraRouter extends AuraRouter
{
    #[DefaultSchemeHost('schemeHost')]
    public function __construct(
        RouterContainer $routerContainer,
        HttpMethodParamsInterface $httpMethodParams,
        string $schemeHost = 'page://self',
        WebServerRequestHeaderProvider|null $headerProvider = null,
    ) {
        parent::__construct($routerContainer, $httpMethodParams, $schemeHost, $headerProvider);
    }
}

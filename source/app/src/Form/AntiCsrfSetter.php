<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form;

use Aura\Input\AntiCsrfInterface;
use Ray\Di\Di\Inject;

trait AntiCsrfSetter
{
    #[Inject]
    public function setAntiCsrf(AntiCsrfInterface $antiCsrf): void
    {
        $this->antiCsrf = $antiCsrf;
    }
}

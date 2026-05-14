<?php

declare(strict_types=1);

namespace MyVendor\MyProject\TemplateEngine;

use BEAR\Resource\RenderInterface;
use BEAR\Resource\ResourceObject;
use Koriym\HttpConstants\ResponseHeader;
use Qiq\Template;
use Ray\Aop\WeavedInterface;
use Ray\Di\Di\Named;
use ReflectionClass;

use function assert;
use function is_iterable;
use function is_string;
use function str_replace;
use function strpos;
use function substr;

readonly class QiqRenderer implements RenderInterface
{
    private const int LENGTH_OF_RESOURCE_DIR = 13;

    /** @param array<string, mixed> $vars */
    public function __construct(
        private Template $template,
        #[Named('qiq_vars')]
        private array $vars,
    ) {
    }

    public function render(ResourceObject $ro): string
    {
        if ($ro->code >= 300 && $ro->code < 400) {
            $location = $ro->headers[ResponseHeader::LOCATION];
            $ro->view = <<<HTML
<html lang="ja">
<body>
<p>Redirect to <a href="{$location}">{$location}</a> :)</p>
</body>
</html>
HTML;

            return $ro->view;
        }

        $template = clone $this->template;
        $this->setTemplateView($template, $ro);
        $template->addData($this->vars);
        if (is_iterable($ro->body)) {
            $template->addData($ro->body);
        }

        $ro->view = $template();

        return $ro->view;
    }

    private function setTemplateView(Template $template, ResourceObject $ro): void
    {
        $fileName = $this->getReflection($ro)->getFileName();
        assert(is_string($fileName));

        $pos = strpos($fileName, 'src/Resource/');
        $relativePath = substr($fileName, (int) $pos + self::LENGTH_OF_RESOURCE_DIR);

        $view = str_replace('.php', '', $relativePath);
        $template->setView($view);
    }

    /** @return ReflectionClass<ResourceObject> */
    private function getReflection(ResourceObject $ro): ReflectionClass
    {
        if ($ro instanceof WeavedInterface) {
            /** @var ReflectionClass<ResourceObject> $parentClass */
            $parentClass = (new ReflectionClass($ro))->getParentClass();

            return $parentClass;
        }

        return new ReflectionClass($ro);
    }
}

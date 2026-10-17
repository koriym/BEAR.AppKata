<?php

declare(strict_types=1);

namespace MyVendor\MyProject\TemplateEngine;

use BEAR\Package\Provide\Error\Status;
use BEAR\Resource\ResourceObject;
use BEAR\Sunday\Extension\Router\RouterMatch;
use Qiq\Template;
use Throwable;

final class QiqErrorPage extends ResourceObject
{
    public function __construct(
        private readonly Throwable $e,
        private readonly string $errorViewName,
        private readonly RouterMatch $request,
        private readonly Template $template,
    ) {
        $status = new Status($e);
        $this->code = $status->code;
        $this->headers = ['Content-Type' => 'text/html; charset=UTF-8'];
        $this->body = [
            'code' => $status->code,
            'message' => $status->text,
            'e' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'class' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
            'request' => (string) $request,
            'stacktrace' => $e->getTraceAsString(),
        ];
    }

    public function toString(): string
    {
        $template = clone $this->template;
        $template->setView($this->errorViewName);
        $template->setData($this->body ?? []);

        $this->view = $template();

        return $this->view;
    }
}

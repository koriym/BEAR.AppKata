<?php

declare(strict_types=1);

namespace MyVendor\MyProject\TemplateEngine;

use Aura\Input\Exception\CsrfViolation;
use BEAR\Package\Provide\Error\Status;
use BEAR\Resource\ResourceObject;
use BEAR\Sunday\Extension\Router\RouterMatch;
use Koriym\HttpConstants\StatusCode;
use Qiq\Template;
use Throwable;

final class QiqErrorPage extends ResourceObject
{
    public function __construct(
        Throwable $e,
        private readonly string $errorViewName,
        RouterMatch $request,
        private readonly Template $template,
    ) {
        [$code, $message] = $this->status($e);
        $this->code = $code;
        $this->headers = ['Content-Type' => 'text/html; charset=UTF-8'];
        $this->body = [
            'code' => $code,
            'message' => $message,
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

    /** @return array{int, string} */
    private function status(Throwable $e): array
    {
        // A CSRF token mismatch is a client error; Status would report it as 500.
        if ($e instanceof CsrfViolation) {
            $code = StatusCode::BAD_REQUEST;

            return [$code, (new StatusCode())->statusText[$code]];
        }

        $status = new Status($e);

        return [$status->code, $status->text];
    }
}

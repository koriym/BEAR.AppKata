<?php

declare(strict_types=1);

namespace MyVendor\MyProject\TemplateEngine;

use Aura\Html\Helper\Input\AbstractInput;
use Aura\Session\CsrfToken;
use BEAR\Sunday\Extension\Router\RouterInterface;
use MyVendor\MyProject\Form\ExtendedFieldset;
use MyVendor\MyProject\Form\ExtendedForm;
use MyVendor\MyProject\Interceptor\CsrfGuard;
use Override;
use Qiq\Helper\Html\HtmlHelpers;
use Stringable;

use function array_merge;
use function is_string;
use function sprintf;
use function strtolower;

/**
 * @SuppressWarnings("PHPMD.ExcessiveClassComplexity")
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class QiqCustomHelpers extends HtmlHelpers
{
    /** @SuppressWarnings("PHPMD.LongVariable") */
    public function __construct(
        private readonly RouterInterface $router,
        private readonly CsrfToken $csrfToken,
    ) {
        parent::__construct(null);
    }

    /**
     * Opens a form. Templates use this instead of a literal <form> tag so that
     * every state-changing form carries the CSRF token without asking for it.
     *
     * @param array<scalar|Stringable|array<scalar|Stringable|null>|null> $attr
     * @param scalar|Stringable|array<scalar|Stringable|null>|null        $__attr
     *
     * @SuppressWarnings("PHPMD.CamelCaseParameterName")
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
     */
    // phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

    #[Override]
    public function form(
        string $method = 'post',
        string $action = '',
        string $enctype = 'application/x-www-form-urlencoded',
        array $attr = [],
        mixed ...$__attr,
    ): string {
        $tag = parent::form($method, $action, $enctype, $attr, ...$__attr);
        if (strtolower($method) === 'get') {
            return $tag;
        }

        return $tag . $this->hiddenField(CsrfGuard::FIELD, $this->csrfToken->getValue());
    }

    // phpcs:enable

    /**
     * @SuppressWarnings("PHPMD.LongVariable")
     * @see https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/
     */
    public function cfTurnstileWidget(
        string $cloudflareTurnstileSiteKey,
        string $size = 'normal',
        string $action = 'none',
        string|null $checked = null,
        string|null $expired = null,
        string|null $error = null,
        string|null $timeout = null,
    ): string {
        if ($cloudflareTurnstileSiteKey === '') {
            return '';
        }

        $attribs = [
            'class' => 'cf-turnstile',
            'data-action' => $action,
            'data-language' => 'ja',
            'data-sitekey' => $cloudflareTurnstileSiteKey,
            'data-size' => $size,
            'data-theme' => 'light',
        ];

        if (is_string($checked)) {
            $attribs['data-callback'] = $checked;
        }

        if (is_string($expired)) {
            $attribs['data-expired-callback'] = $expired;
        }

        if (is_string($error)) {
            $attribs['data-error-callback'] = $error;
        }

        if (is_string($timeout)) {
            $attribs['data-timeout-callback'] = $timeout;
        }

        return sprintf('<div %s></div>', $this->a($attribs));
    }

    /** @param array<string, string> $attribs */
    public function adminCheckBox(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => ''];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function adminFieldsetError(
        ExtendedFieldset $fieldset,
        string $input,
        string $tag = 'span',
        array $attribs = [],
    ): string {
        $errorMessages = $fieldset->error($input);
        if (empty($errorMessages)) {
            return '';
        }

        $defaultAttribs = ['class' => 'block text-sm text-rose-500 italic'];

        return sprintf(
            '<%s %s>%s</%s>',
            $tag,
            $this->a(array_merge($defaultAttribs, $attribs)),
            $this->h($errorMessages[0]),
            $tag,
        );
    }

    /** @param array<string, string> $attribs */
    public function adminFile(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => 'rounded w-full p-3 bg-white border border-[#6b7280] placeholder:text-slate-500 placeholder:font-thin focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500'];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function adminFormError(ExtendedForm $form, string $input, string $tag = 'span', array $attribs = []): string
    {
        $message = $form->error($input);
        if ($message === '') {
            return '';
        }

        $defaultAttribs = ['class' => 'block text-sm text-rose-500 italic'];

        return sprintf(
            '<%s %s>%s</%s>',
            $tag,
            $this->a(array_merge($defaultAttribs, $attribs)),
            $this->h($message),
            $tag,
        );
    }

    /** @param array<string, string> $attribs */
    public function adminHidden(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);
        $spec['type'] = 'hidden';

        $defaultAttribs = [];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function adminRadio(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => ''];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function adminSelect(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => 'rounded transition duration-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500'];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function adminText(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => 'rounded w-full placeholder:text-slate-500 placeholder:font-thin transition duration-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500'];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function adminTextArea(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => 'rounded w-full placeholder:text-slate-500 placeholder:font-thin transition duration-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500 leading-6 h-40'];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function adminSubmit(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        if (isset($attribs['value'])) {
            $spec['value'] = $attribs['value'];
            unset($attribs['value']);
        }

        $defaultAttribs = [
            'value' => 'Submit',
            'class' => 'py-2 px-4 bg-indigo-500 text-white text-sm font-sans font-bold tracking-wider rounded-full shadow-lg shadow-indigo-500/50 focus:outline-none hover:bg-indigo-600 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:text-white disabled:bg-slate-200 disabled:shadow-none',
        ];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function managerCheckBox(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => ''];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function managerFieldsetError(
        ExtendedFieldset $fieldset,
        string $input,
        string $tag = 'span',
        array $attribs = [],
    ): string {
        $errorMessages = $fieldset->error($input);
        if (empty($errorMessages)) {
            return '';
        }

        $defaultAttribs = ['class' => 'block text-sm text-rose-500 italic'];

        return sprintf(
            '<%s %s>%s</%s>',
            $tag,
            $this->a(array_merge($defaultAttribs, $attribs)),
            $this->h($errorMessages[0]),
            $tag,
        );
    }

    /** @param array<string, string> $attribs */
    public function managerFile(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => 'rounded w-full p-3 bg-white border border-[#6b7280] placeholder:text-slate-500 placeholder:font-thin focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500'];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function managerFormError(ExtendedForm $form, string $input, string $tag = 'span', array $attribs = []): string
    {
        $message = $form->error($input);
        if ($message === '') {
            return '';
        }

        $defaultAttribs = ['class' => 'block text-sm text-rose-500 italic'];

        return sprintf(
            '<%s %s>%s</%s>',
            $tag,
            $this->a(array_merge($defaultAttribs, $attribs)),
            $this->h($message),
            $tag,
        );
    }

    /** @param array<string, string> $attribs */
    public function managerHidden(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);
        $spec['type'] = 'hidden';

        $defaultAttribs = [];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function managerRadio(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => ''];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function managerSelect(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => 'rounded transition duration-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500'];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function managerText(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => 'rounded w-full placeholder:text-slate-500 placeholder:font-thin transition duration-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500'];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function managerTextArea(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        $defaultAttribs = ['class' => 'rounded w-full placeholder:text-slate-500 placeholder:font-thin transition duration-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500 leading-6 h-40'];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, string> $attribs */
    public function managerSubmit(
        ExtendedForm $form,
        string $input,
        ExtendedFieldset|null $fieldset = null,
        array $attribs = [],
    ): AbstractInput {
        $spec = $fieldset === null ? $form->get($input) : $fieldset->get($input);

        if (isset($attribs['value'])) {
            $spec['value'] = $attribs['value'];
            unset($attribs['value']);
        }

        $defaultAttribs = [
            'value' => 'Submit',
            'class' => 'py-2 px-4 bg-indigo-500 text-white text-sm font-sans font-bold tracking-wider rounded-full shadow-lg shadow-indigo-500/50 focus:outline-none hover:bg-indigo-600 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:text-white disabled:bg-slate-200 disabled:shadow-none',
        ];

        return $form->widget($spec, array_merge($defaultAttribs, $attribs));
    }

    /** @param array<string, mixed> $params */
    public function url(string $routePath, array $params = []): string
    {
        $path = $this->router->generate($routePath, $params);
        if (is_string($path)) {
            return $path;
        }

        return $routePath;
    }
}

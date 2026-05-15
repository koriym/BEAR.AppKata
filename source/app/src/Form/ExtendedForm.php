<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Form;

use Aura\Filter\FilterFactory;
use Aura\Html\Helper\Input\AbstractInput;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\BuilderInterface;
use Aura\Input\Collection;
use Aura\Input\Fieldset;
use Override;
use Ray\Di\Di\Inject;
use Ray\Di\Di\PostConstruct;
use Ray\WebFormModule\AbstractForm;
use Ray\WebFormModule\SubmitInterface;

use function array_merge;
use function assert;
use function is_array;

/** @SuppressWarnings("PHPMD.NumberOfChildren") */
abstract class ExtendedForm extends AbstractForm implements SubmitInterface
{
    #[Inject]
    #[Override]
    public function setBaseDependencies(
        BuilderInterface $builder,
        FilterFactory $filterFactory,
        HelperLocatorFactory $helperFactory,
    ): void {
        parent::setBaseDependencies($builder, $filterFactory, $helperFactory);
    }

    #[PostConstruct]
    #[Override]
    public function postConstruct(): void
    {
        parent::postConstruct();
    }

    public function setOptions(): void
    {
        // NOTE: This method is child class override.
    }

    /** @return array<string, mixed> */
    public function getData(): array
    {
        return $this->getValue();
    }

    /**
     * @param string|array<string, string|int> $nameOrSpec
     * @param array<string, mixed>             $attribs
     */
    public function widget(string|array $nameOrSpec, array $attribs = []): AbstractInput
    {
        $array = is_array($nameOrSpec) ? $nameOrSpec : $this->get($nameOrSpec);
        if (is_array($array)) {
            $array['attribs'] = array_merge($array['attribs'] ?? [], $attribs);
        }

        return $this->helper->input($array); // @phpstan-ignore-line
    }

    /** @param array<array-key, mixed> $data */
    public function apply(array $data): bool
    {
        $isValid = parent::apply($data);

        foreach ($this->inputs as $input) {
            if ($input instanceof Fieldset) {
                $input->filter();
                continue;
            }

            if (! ($input instanceof Collection)) {
                continue;
            }

            $fieldsetName = $input->name;
            foreach ($this->$fieldsetName as $fieldset) {
                assert($fieldset instanceof Fieldset);
                $fieldset->filter();
            }
        }

        return $isValid;
    }

    /**
     * @return array<string, mixed>
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     *
     * @SuppressWarnings("PHPMD.Superglobals")
     *
     * @deprecated
     */
    public function submit(): array
    {
        return $_POST;
    }
}

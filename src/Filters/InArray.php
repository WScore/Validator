<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class InArray extends AbstractFilter
{
    /**
     * @var array
     */
    private $choices;

    /**
     * @var bool
     */
    private $strict = true;

    /**
     * @var bool
     */
    private $replace;

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->choices = $options['choices'] ?? [];
        $this->replace = $options['replace'] ?? false;
        $this->strict = $options['strict'] ?? true;
        $this->setPriority(FilterInterface::PRIORITY_FILTER_MODIFIER);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if (in_array($value, $this->choices, $this->strict)) {
            if ($this->replace) {
                $input->setValue($this->choices[$value]);
            }
            return null;
        } else {
            return $value;
        }
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class DefaultValue extends AbstractValidator
{
    /**
     * @var mixed
     */
    private $default;

    /**
     * DefaultValue constructor.
     * @param mixed $default
     */
    public function __construct($default)
    {
        $this->default = $default;
        $this->setPriority(FilterInterface::PRIORITY_USER_FILTERS - 1);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if ('' !== (string) $value) {
            return null;
        }
        $input->setValue($this->default);
        return null;
    }
}
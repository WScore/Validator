<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class DefaultValue implements FilterInterface
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
    }

    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input, ResultInterface $allInputs): ?ResultInterface
    {
        $value = $input->value();
        if ('' !== (string) $value) {
            return null;
        }
        $input->setValue($this->default);
        return null;
    }
}
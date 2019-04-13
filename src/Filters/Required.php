<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class Required extends AbstractFilter
{
    public function __construct()
    {
        $this->setPriority(FilterInterface::PRIORITY_REQUIRED_CHECK);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if ($this->isEmpty($value)) {
            return $this->failed($input);
        }
        return null;
    }
}
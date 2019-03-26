<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class FilterValidUtf8 implements FilterInterface
{
    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if (mb_check_encoding($value, 'UTF-8')) {
            return null;
        }
        if (is_array($value)) {
            $input->setValue([]);
        } else {
            $input->setValue('');
        }
        $input->failed(__CLASS__);
        return $input;
    }

    /**
     * returns the priority of the filter.
     * applies filters with smaller priority, first.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return FilterInterface::PRIORITY_SECURITY_FILTERS;
    }

    /**
     * returns name of the filter;
     * validation can have only one filter with the same name.
     *
     * @return string
     */
    public function getFilterName(): string
    {
        return __CLASS__;
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Interfaces;

/**
 * define filter for $input values.
 * - filters: modify value by $input->setValue($new_value);
 * - validators: set fail by $result->failed($message);
 * to stop further filter chain, return $result object.
 */
interface FilterInterface
{
    const ADD_APPEND = 'append';
    const ADD_PREPEND = 'prepend';

    /**
     * @return string
     */
    public function getAddType(): string;

    /**
     * returns name of the filter;
     * validation can have only one filter with the same name.
     *
     * @return string
     */
    public function getFilterName(): string;

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface;
}

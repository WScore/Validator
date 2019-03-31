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
    const PRIORITY_SECURITY_FILTERS = 100;
    const PRIORITY_STRING_FILTERS   = 1000;
    const PRIORITY_USER_FILTERS     = 5000;
    const PRIORITY_REQUIRED_FILTERS = 10000;
    const PRIORITY_VALIDATIONS      = 20000;
    const PRIORITY_USER_VALIDATIONS = 30000;

    /**
     * returns the priority of the filter.
     * applies filters with smaller priority, first.
     *
     * @return int
     */
    public function getPriority(): int;

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
    public function __invoke(ResultInterface $input): ?ResultInterface;
}

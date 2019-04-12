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
    const PRIORITY_FILTER_PREPARE = 1000;
    const PRIORITY_FILTER_SANITIZE = 1100;
    const PRIORITY_FILTER_MODIFIER = 1200;
    const PRIORITY_FILTER_BY_USERS = 1500;
    const PRIORITY_REQUIRED_CHECK = 2000;
    const PRIORITY_VALIDATIONS = 2100;
    const PRIORITY_VALIDATION_BY_USERS = 3000;

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

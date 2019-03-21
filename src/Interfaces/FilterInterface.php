<?php

namespace WScore\Validation\Interfaces;

/**
 * define filter for $input values.
 * - filters: modify value by $input->setValue($new_value);
 * - validators: set fail by $result->failed($message);
 * to stop further filter chain, return $result object.
 */
interface FilterInterface
{
    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input, ResultInterface$allInputs): ?ResultInterface;
}

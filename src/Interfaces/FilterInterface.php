<?php

namespace WScore\Validation\Interfaces;

/**
 * Interface FilterInterface
 * @package WScore\FormModel\Interfaces
 */
interface FilterInterface
{
    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke($input, $allInputs);
}

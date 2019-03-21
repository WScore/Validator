<?php

namespace WScore\FormModel\Validation;

/**
 * Interface FilterInterface
 * @package WScore\FormModel\Interfaces
 */
interface ValidatorInterface
{
    /**
     * @param ResultInterface $result
     * @param array $allInput
     * @return Result|null
     */
    public function __invoke(ResultInterface $result, $allInput = []): ?Result;
}

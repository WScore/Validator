<?php

namespace WScore\Validation\Interfaces;

/**
 * ValidatorInterface is exactly the same as FilterInterface.
 */
interface ValidatorInterface
{
    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input, ResultInterface$allInputs): ?ResultInterface;
}

<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class FilterValidUtf8 implements FilterInterface
{
    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input, ResultInterface $allInputs): ?ResultInterface
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
        $input->failed('invalid encoding');
        return $input;
    }
}
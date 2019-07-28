<?php
declare(strict_types=1);

namespace WScore\Validator\Filters;

use WScore\Validator\Interfaces\ResultInterface;

trait ValidateUtf8Trait
{
    /**
     * checks for valid UTF-8 characters, not an array, and length not too long.
     * returns Result object when fails.
     * otherwise returns null.
     *
     * @param ResultInterface $input
     * @param int $max
     * @return ResultInterface|null
     */
    public function checkUtf8(ResultInterface $input, int $max = null): ?ResultInterface
    {
        $max = $max ?? 1028 * 1028;
        $value = $input->value();
        if (is_array($value)) {
            $input->setValue(null);
            return $input->failed(ValidateUtf8String::ERROR_ARRAY_INPUT);
        }
        if (!mb_check_encoding($value, 'UTF-8')) {
            $input->setValue(null);
            return $input->failed(ValidateUtf8String::ERROR_INVALID_CHAR);
        }
        if (strlen((string) $value) > $max) {
            $input->setValue(null);
            return $input->failed(ValidateUtf8String::ERROR_INPUT_SIZE_MAX, ['max' => $max]);
        }
        return null;
    }

}
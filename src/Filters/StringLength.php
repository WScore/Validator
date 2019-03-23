<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class StringLength extends AbstractMultipleValidator
{
    /**
     * @var null|int
     */
    private $max = null;

    /**
     * @var null|int
     */
    private $min = null;

    /**
     * @var null|int
     */
    private $length = null;

    /**
     * @param string $value
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return string|ResultInterface|null
     */
    public function validate($value, ResultInterface $input, ResultInterface $allInputs)
    {
        $length = mb_strlen($value);
        if ($this->length !== null) {
            return $this->checkLength($input, $length);
        }
        if ($this->max !== null) {
            return $this->checkMax($input, $length);
        }
        if ($this->min !== null) {
            return $this->checkMin($input, $length);
        }
        return null;
    }

    /**
     * @param int|null $max
     * @return StringLength
     */
    public function setMax(int $max): StringLength
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param int|null $min
     * @return StringLength
     */
    public function setMin(int $min): StringLength
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param int|null $length
     * @return StringLength
     */
    public function setLength(int $length): StringLength
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @param ResultInterface $input
     * @param int $length
     * @return ResultInterface|null
     */
    private function checkLength(ResultInterface $input, int $length)
    {
        if ($this->length !== $length) {
            $input->failed('length must be');
            return $input;
        }
        return null;
    }

    /**
     * @param ResultInterface $input
     * @param int $length
     * @return ResultInterface|null
     */
    private function checkMax(ResultInterface $input, int $length)
    {
        if ($this->max < $length) {
            $input->failed('out of max');
            return $input;
        }
        return null;
    }

    /**
     * @param ResultInterface $input
     * @param int $length
     * @return ResultInterface|null
     */
    private function checkMin(ResultInterface $input, int $length)
    {
        if ($length < $this->min) {
            $input->failed('out of min');
            return $input;
        }
        return null;
    }

    /**
     * returns the priority of the filter.
     * applies filters with smaller priority, first.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return FilterInterface::PRIORITY_VALIDATIONS;
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
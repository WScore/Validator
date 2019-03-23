<?php
namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class InArray extends AbstractMultipleValidator
{
    /**
     * @var array
     */
    private $inArray;

    /**
     * @var bool
     */
    private $strict = true;

    /**
     * @param array $inArray
     */
    public function __construct(array $inArray)
    {
        $this->inArray = $inArray;
    }

    /**
     * @param string $value
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return string|null
     */
    public function validate($value, ResultInterface $input, ResultInterface $allInputs)
    {
        if (in_array($value, $this->inArray, $this->strict)) {
            return null;
        } else {
            return $value;
        }
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
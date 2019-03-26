<?php
namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class InArray implements FilterInterface
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
     * returns the priority of the filter.
     * applies filters with smaller priority, first.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return FilterInterface::PRIORITY_STRING_FILTERS;
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

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if (in_array($value, $this->inArray, $this->strict)) {
            return null;
        } else {
            return $value;
        }
    }
}
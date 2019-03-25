<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;

abstract class AbstractValidator implements FilterInterface
{
    private $priority = FilterInterface::PRIORITY_VALIDATIONS;

    /**
     * returns the priority of the filter.
     * applies filters with smaller priority, first.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
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
     * @param int $priority
     * @return AbstractValidator
     */
    public function setPriority(int $priority): AbstractValidator
    {
        $this->priority = $priority;
        return $this;
    }
}
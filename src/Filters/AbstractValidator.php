<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

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
        return str_replace(__NAMESPACE__.'\\', '', __CLASS__);
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

    /**
     * @param ResultInterface $result
     * @param array $option
     * @param string $messages
     * @return ResultInterface
     */
    protected function failed(ResultInterface $result, $option = [], $messages = null)
    {
        return $result->failed(get_class($this), $option, $messages);
    }
}
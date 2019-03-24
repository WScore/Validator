<?php

namespace WScore\Validation;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Interfaces\ValidationInterface;

abstract class AbstractValidation implements ValidationInterface
{
    /**
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * @var ValidationInterface[]
     */
    protected $children = [];

    /**
     * @var string
     */
    protected $message = null;

    public function setErrorMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param callable[]|FilterInterface[] $filters
     * @return $this
     */
    public function addFilters(callable ...$filters)
    {
        foreach ($filters as $filter) {
            $this->filters[$filter->getFilterName()] = $filter;
        }
        return $this;
    }

    /**
     * @param string $name
     * @param ValidationInterface $validation
     * @return void
     */
    public function addChild(string $name, ValidationInterface $validation)
    {
        $this->children[$name] = $validation;
    }

    protected function sortFilters()
    {
        usort(
            $this->filters,
            function (FilterInterface $a, FilterInterface $b) {
                return $a->getPriority() <=> $b->getPriority();
            });
    }

    protected function applyFilters(ResultInterface $result, ResultInterface $rootResults = null)
    {
        $this->sortFilters();
        foreach ($this->filters as $filter) {
            if ($result = $filter->__invoke($result, $rootResults)) {
                return $result;
            }
        }
        if (!$result->isValid() && $this->message !== null) {
            $result->failed($this->message);
        }
        return $result;
    }
}
<?php

namespace WScore\Validation;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\Locale\Messages;

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
    protected $error_message = null;

    /**
     * @var Messages
     */
    protected $message;

    /**
     * @param Messages $message
     */
    public function __construct(Messages $message)
    {
        $this->message = $message;
    }

    /**
     * @param string $message
     */
    public function setErrorMessage(string $message)
    {
        $this->error_message = $message;
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

    protected function prepareFilters()
    {
        foreach ($this->filters as $name => $filter) {
            if (!is_object($filter)) {
                $this->filters[$name] = new $name($filter);
            }
        }
        usort(
            $this->filters,
            function (FilterInterface $a, FilterInterface $b) {
                return $a->getPriority() <=> $b->getPriority();
            });
    }

    protected function applyFilters(ResultInterface $result, ResultInterface $rootResults = null)
    {
        foreach ($this->filters as $filter) {
            if ($result = $filter->__invoke($result, $rootResults)) {
                return $result;
            }
        }
        return $result;
    }
}
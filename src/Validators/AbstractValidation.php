<?php

namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\Locale\Messages;

abstract class AbstractValidation implements ValidationInterface
{
    /**
     * @var string|null
     */
    protected $name;

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
     * @param string|null $name
     */
    public function __construct(Messages $message, $name = null)
    {
        $this->message = $message;
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return AbstractValidation
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * TODO: when invalid, overwrite error messages in $result with errorMessage.
     *
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
    public function addFilters(FilterInterface ...$filters): ValidationInterface
    {
        foreach ($filters as $filter) {
            $this->filters[$filter->getFilterName()] = $filter;
        }
        return $this;
    }

    /**
     * @param string $name
     * @param ValidationInterface $validation
     * @return ValidationInterface
     */
    public function add(string $name, ValidationInterface $validation): ValidationInterface
    {
        $validation->setName($name);
        $this->children[$name] = $validation;
        return $this;
    }

    /**
     * @param string $name
     * @return ValidationInterface
     */
    public function get(string $name): ?ValidationInterface
    {
        return $this->children[$name] ?? null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->children);
    }

    /**
     * @param string $name
     * @return ValidationInterface
     */
    public function remove(string $name): ValidationInterface
    {
        unset($this->children[$name]);
        return $this;
    }

    /**
     * @return ValidationInterface[]
     */
    public function all(): array
    {
        return $this->children;
    }

    protected function prepareFilters()
    {
        foreach ($this->filters as $name => $filter) {
            if (!is_object($filter)) {
                $this->filters[$name] = new $name($filter);
            }
        }
        uasort(
            $this->filters,
            function (FilterInterface $a, FilterInterface $b) {
                return $a->getPriority() <=> $b->getPriority();
            });
    }

    protected function applyFilters(ResultInterface $result)
    {
        foreach ($this->filters as $filter) {
            if ($returned = $filter->__invoke($result)) {
                return $returned;
            }
        }
        return $result;
    }
}
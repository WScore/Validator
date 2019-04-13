<?php
declare(strict_types=1);

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
     * @return ValidationInterface|$this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $message
     */
    public function setErrorMessage(string $message)
    {
        $this->error_message = $message;
    }

    /**
     * @param FilterInterface[] $filters
     * @return ValidationInterface|$this
     */
    public function addFilters(array $filters): ValidationInterface
    {
        foreach ($filters as $key => $filter) {
            if ($filter instanceof FilterInterface) {
                // $filter = $filter;
            } elseif (is_numeric($key) && is_string($filter)) {
                $filter = new $filter();
            } else {
                $filter = new $key($filter);
            }
            $this->filters[$filter->getFilterName()] = $filter;
        }
        uasort(
            $this->filters,
            function (FilterInterface $a, FilterInterface $b) {
                return $a->getPriority() <=> $b->getPriority();
            });
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasFilter(string $name): bool
    {
        return array_key_exists($name, $this->filters);
    }

    /**
     * @param string $name
     * @return FilterInterface
     */
    public function getFilter(string $name): FilterInterface
    {
        return $this->filters[$name] ?? null;
    }

    /**
     * @param string $name
     * @return ValidationInterface|$this
     */
    public function removeFilter(string $name): ValidationInterface
    {
        if (isset($this->filters[$name])) {
            unset($this->filters[$name]);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param ValidationInterface $validation
     * @return ValidationInterface|$this
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
     * @return ValidationInterface|$this
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

    protected function applyFilters(ResultInterface $result)
    {
        foreach ($this->filters as $filter) {
            if ($returned = $filter->apply($result)) {
                return $returned;
            }
        }
        return $result;
    }
}
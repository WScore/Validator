<?php
declare(strict_types=1);

namespace WScore\Validator\Validators;

use WScore\Validator\Interfaces\FilterCollectionInterface;
use WScore\Validator\Interfaces\FilterInterface;
use WScore\Validator\Interfaces\ResultInterface;
use WScore\Validator\Interfaces\ValidationInterface;
use WScore\Validator\Locale\Messages;

abstract class AbstractValidation implements ValidationInterface
{
    /**
     * @var FilterCollection|FilterInterface[]
     */
    protected $preFilters;

    /**
     * @var FilterCollection|FilterInterface[]
     */
    protected $filters;

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
        $this->filters = new FilterCollection();
        $this->preFilters = new FilterCollection();
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
     * @param FilterInterface[] $filters
     * @return ValidationList
     */
    public function addPreparationFilters(FilterInterface ...$filters): ValidationInterface
    {
        $this->preFilters->addFilters($filters);
        return $this;
    }

    /**
     * @return FilterCollectionInterface
     */
    public function getPreparationFilters(): FilterCollectionInterface
    {
        return $this->preFilters;
    }

    /**
     * @param FilterInterface[] $filters
     * @return ValidationInterface|$this
     */
    public function addFilters(array $filters): ValidationInterface
    {
        $this->filters->addFilters($filters);
        return $this;
    }

    /**
     * @return FilterCollectionInterface
     */
    public function getFilters(): FilterCollectionInterface
    {
        return $this->filters;
    }

    /**
     * @param string $name
     * @param ValidationInterface $validation
     * @return ValidationInterface|$this
     */
    public function add(string $name, ValidationInterface $validation): ValidationInterface
    {
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
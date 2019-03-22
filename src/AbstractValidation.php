<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\Interfaces\ValidatorInterface;

abstract class AbstractValidation implements ValidationInterface
{
    /**
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * @var ValidatorInterface[]
     */
    protected $validators = [];

    /**
     * @var ValidationInterface[]
     */
    protected $children = [];

    /**
     * @param callable[]|FilterInterface[] $filters
     * @return $this
     */
    public function setInputFilter(callable ...$filters)
    {
        $this->filters = array_merge($this->filters, $filters);
        return $this;
    }

    /**
     * @param callable[]|ValidatorInterface[] $validators
     * @return $this
     */
    public function setValidator(callable ...$validators)
    {
        $this->validators = array_merge($this->validators, $validators);
        return $this;
    }

    /**
     * @param ValidationInterface $validation
     * @return void
     */
    public function addChild(ValidationInterface $validation)
    {
        $this->children[] = $validation;
    }
}
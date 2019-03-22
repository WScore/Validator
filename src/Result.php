<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;

class Result implements ResultInterface
{
    /**
     * @var null|mixed
     */
    private $value = null;

    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var string[]
     */
    private $message = [];

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var ResultList
     */
    private $parent;

    /**
     * @param string $message
     * @return ResultInterface
     */
    public function failed(string $message): ResultInterface
    {
        $this->message[] = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string|string[]|mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasChild(string $name): bool
    {
        return false;
    }

    /**
     * @param string $name
     * @return ResultInterface
     */
    public function getChild(string $name): ?ResultInterface
    {
        return null;
    }

    /**
     * @return string[]
     */
    public function getErrorMessage()
    {
        return $this->message;
    }

    /**
     * @return self[]|\iterable
     */
    public function getIterator()
    {
        return new \ArrayIterator([]);
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return false;
    }

    /**
     * @return self[]
     */
    public function getChildren()
    {
        return [];
    }

    /**
     * @return ResultList|null
     */
    public function getParent(): ?ResultList
    {
        return $this->parent;
    }

    /**
     * @param ResultList $parent
     */
    public function setParent(ResultList $parent): void
    {
        $this->parent = $parent;
    }
}
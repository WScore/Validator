<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;

class ResultList implements ResultInterface
{
    /**
     * @var null|array
     */
    private $value = [];

    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var array
     */
    private $message = [];

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $label = '';

    /**
     * @var Result[]
     */
    private $children = [];

    /**
     * @var ResultInterface
     */
    private $parent;

    public function addResult(ResultInterface $result, $name = null)
    {
        $result->setParent($this);
        $name = $name ?? $result->name();
        $this->value[$name] = $result->value();
        $this->message[$name] = $result->getErrorMessage();
        $this->children[$name] = $result;
        if (!$result->isValid()) {
            $this->isValid = false;
        }
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
     * @return string
     */
    public function label(): string
    {
        return $this->label;
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
        return isset($this->children[$name]);
    }

    /**
     * @param string $name
     * @return ResultInterface
     */
    public function getChild(string $name): ?ResultInterface
    {
        return $this->children[$name] ?? null;
    }

    /**
     * @return string|string[]|mixed
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
        return new \ArrayIterator($this->getChildren());
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return count($this->children);
    }

    /**
     * @return ResultInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return ResultInterface
     */
    public function getParent(): ResultInterface
    {
        return $this->parent;
    }

    /**
     * @param ResultInterface $parent
     */
    public function setParent(ResultInterface $parent): void
    {
        $this->parent = $parent;
    }
}
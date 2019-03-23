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
     * @var Result[]
     */
    private $children = [];

    /**
     * @var ResultList
     */
    private $parent;

    public function __construct($message = null)
    {
        $this->message = $message ? [$message]: [];
    }

    public function addResult(ResultInterface $result, $name = null)
    {
        $result->setParent($this);
        $name = $name ?? $result->name();
        $this->children[$name] = $result;
        $this->value[$name] = $result->value();
        $this->message[$name] = $result->getErrorMessage();
        if (!$result->isValid()) {
            $this->isValid = false;
        }
    }

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
        if ($this->isValid === false) return false;
        foreach ($this->children as $child) {
            if (!$child->isValid()) {
                $this->isValid = false;
            }
        }
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

    public function summarizeValues(): array
    {
        return $this->summarizeChildren('value');
    }

    public function summarizeErrorMessages(): array
    {
        return $this->summarizeChildren('getErrorMessage');
    }

    /**
     * @param string $method
     * @return array
     */
    private function summarizeChildren(string $method): array
    {
        $values = [];
        foreach ($this->children as $name => $child) {
            $values[$name] = $child->$method();
        }
        return $values;
    }
}
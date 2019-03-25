<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Locale\Messages;

class ResultList implements ResultInterface
{
    /**
     * @var null|array
     */
    private $value = [];

    /**
     * @var null|array
     */
    private $originalValue = [];

    /**
     * @var bool
     */
    private $isValid = true;

    /**
     * @var Messages
     */
    private $message;

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

    /**
     * @var array
     */
    private $failed = [];

    /**
     * Result constructor.
     * @param Messages|null $message
     * @param string $value
     * @param null|string $name
     */
    public function __construct(?Messages $message, $value, $name = null)
    {
        $this->message = $message;
        $this->value = $this->originalValue = $value;
        $this->name = $name;
    }

    /**
     * @param ResultInterface $result
     * @param null $name
     */
    public function addResult(ResultInterface $result, $name = null)
    {
        $result->setParent($this);
        $name = $name ?? $result->name();
        $this->children[$name] = $result;
    }

    /**
     * @param string $failedAt
     * @param array $options
     * @param string $message
     * @return $this
     */
    public function failed(string $failedAt, array $options = [], string $message = null): ResultInterface
    {
        if ($message === null && $this->message) {
            $message = $this->message->getMessage($failedAt, $options);
        }
        $this->failed[] = [
            'failedAt' => $failedAt,
            'options' => $options,
            'message' => $message
        ];
        $this->isValid = false;
        return $this;
    }

    /**
     * @return string
     */
    public function name(): ?string
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
     * @return string|string[]|array
     */
    public function getOriginalValue()
    {
        return $this->originalValue;
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
        if ($this->isValid()) {
            return [];
        }
        $messages = [];
        foreach ($this->failed as $item) {
            $messages[] = $item['message'];
        }
        return $messages;
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

    /**
     * @return  void
     */
    public function finalize()
    {
        $this->summarizeChildren('finalize');
        $this->value = $this->summarizeChildren('value');
        $this->isValid = true;
        foreach ($this->children as $name => $child) {
            if (!$child->isValid()) {
                $this->isValid = false;
            }
        }
    }

    public function summarizeErrorMessages(): array
    {
        $messages = $this->summarizeChildren('getErrorMessage');
        $messages = array_merge($this->getErrorMessage(), $messages);
        return $messages;
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
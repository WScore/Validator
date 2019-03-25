<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Locale\Messages;

class Result implements ResultInterface
{
    /**
     * @var null|mixed
     */
    private $value = null;

    /**
     * @var null|mixed
     */
    private $originalValue = null;

    /**
     * @var bool
     */
    private $isValid = true;

    /**
     * @var null|Messages
     */
    private $message = null;

    /**
     * @var string
     */
    private $name = '';

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
     * @param null|Messages $message
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

    /**
     * @return  void
     */
    public function finalize()
    {
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use ArrayIterator;
use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Locale\Messages;

abstract class AbstractResult implements ResultInterface
{
    /**
     * @var bool
     */
    protected $isValid = true;
    /**
     * @var string[]
     */
    protected $messages = [];
    /**
     * @var Result[]
     */
    protected $children = [];
    /**
     * @var array
     */
    protected $failed = [];
    /**
     * @var null|array
     */
    private $value = [];
    /**
     * @var null|array
     */
    private $originalValue = [];
    /**
     * @var string
     */
    private $name = '';
    /**
     * @var ResultList
     */
    private $parent;

    /**
     * Result constructor.
     * @param string|array $value
     * @param null|string $name
     */
    public function __construct($value, $name = null)
    {
        $this->value = $this->originalValue = $value;
        $this->name = (string) $name;
    }

    /**
     * @param string $failedAt
     * @param array $options
     * @param string $message
     * @return $this
     */
    public function failed(string $failedAt, array $options = [], string $message = null): ResultInterface
    {
        $this->failed[] = [
            'failedAt' => $failedAt,
            'options' => $options,
            'message' => $message
        ];
        $this->isValid = false;
        return $this;
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
     * @param string $name
     * @return bool
     */
    public function hasChild(string $name): bool
    {
        return isset($this->children[$name]);
    }

    /**
     * @param string|int $name
     * @return ResultInterface
     */
    public function getChild($name): ?ResultInterface
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
        return $this->messages;
    }

    /**
     * @return self[]|iterable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getChildren());
    }

    /**
     * @return ResultInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * @return ResultInterface|null
     */
    public function getRoot(): ?ResultInterface
    {
        $root = $this->getParent();
        while ($root) {
            if (!$newRoot = $root->getParent()) {
                return $root;
            }
            $root = $newRoot;
        }
        return $root;
    }

    /**
     * @return ResultList|null
     */
    public function getParent(): ?ResultList
    {
        return $this->parent;
    }

    /**
     * @param ResultInterface $parent
     */
    public function setParent(ResultInterface $parent = null): void
    {
        $this->parent = $parent;
    }

    protected function populateMessages(?Messages $messages)
    {
        $this->messages = [];
        if ($this->isValid()) return;

        foreach ($this->failed as $item) {
            $message = $item['message'] ?? null;
            if ($message === null && $messages) {
                $options = $item['options'];
                if ($this->name) {
                    $options += ['name' => $this->name()];
                }
                $message = $messages->getMessage($item['failedAt'], $options);
            }
            if ((string) $message !== '') {
                $this->messages[] = $message;
            }
        }
        if (empty($this->messages) && $messages) {
            $this->messages[] = $messages->getMessage(Messages::class, []);
        }
    }

    /**
     * @return string
     */
    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * @param mixed $offset
     * @return ResultInterface|Result|null
     */
    public function offsetGet($offset)
    {
        return $this->children[$offset] ?? null;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->children);
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }
}
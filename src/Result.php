<?php
namespace WScore\FormModel\Validation;

use WScore\FormModel\Interfaces\BaseElementInterface;

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
     * @var string
     */
    private $message = '';

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $label = '';

    /**
     * @param BaseElementInterface $element
     * @param mixed $value
     * @return ResultInterface
     */
    public static function success(BaseElementInterface $element, $value): ResultInterface
    {
        $self = new self();
        $self->fillResult($element);
        $self->isValid = true;
        $self->value = $value;

        return $self;
    }

    /**
     * @param BaseElementInterface $element
     * @param mixed $value
     * @param string $message
     * @return ResultInterface
     */
    public static function fail(?BaseElementInterface $element, $value, $message): ResultInterface
    {
        $self = new self();
        $self->fillResult($element);
        $self->isValid = false;
        $self->message = $message;
        $self->value = $value;

        return $self;
    }

    private function fillResult(BaseElementInterface $element)
    {
        $this->name = $element->getName();
        $this->label = $element->getLabel();
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
}
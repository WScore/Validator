<?php
namespace WScore\FormModel\Validation;

use WScore\FormModel\Interfaces\FormElementInterface;

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
     * @param FormElementInterface $form
     * @param ResultInterface[] $results
     * @return ResultInterface
     */
    public static function aggregate(FormElementInterface $form, array $results): ResultInterface
    {
        if (empty($results)) {
            throw new \InvalidArgumentException('empty results');
        }
        $self = new self();
        $self->fillResult($form);
        $isValid = true;
        foreach ($results as $name => $result) {
            if (!$result->isValid()) {
                $isValid = false;
            }
            $self->value[$name] = $result->value();
            $self->message[$name] = $result->getErrorMessage();
            $self->children[$name] = $result;
        }
        $self->isValid = $isValid;

        return $self;
    }

    public function addResult(ResultInterface $result, $name = null)
    {
        $name = $name ?? $result->name();
        $this->value[$name] = $result->value();
        $this->message[$name] = $result->getErrorMessage();
        $this->children[$name] = $result;
        if (!$result->isValid()) {
            $this->isValid = false;
        }
    }

    private function fillResult(FormElementInterface $element)
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
}
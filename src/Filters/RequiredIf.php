<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use InvalidArgumentException;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class RequiredIf extends AbstractFilter
{
    const FIELD = 'field';
    const VALUE = 'value';
    const NULLABLE = 'nullable';

    /**
     * @var string|null
     */
    private $field;

    /**
     * @var string|int|array|null
     */
    private $value;

    /**
     * @var bool|mixed|null
     */
    private $nullable = false;

    public function __construct($options = [])
    {
        $this->field = $options[self::FIELD] ?? null;
        $this->value = $options[self::VALUE] ?? null;
        $this->nullable = $options[self::NULLABLE] ?? null;
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if ($this->hasValue($value)) {
            return null;
        }
        $isRequired = $this->checkCondition($input);
        if ($isRequired) {
            return $input->failed(Required::class);
        }
        if ($this->nullable) {
            return $input;
        }
        return null;
    }

    private function checkCondition(ResultInterface $input): bool
    {
        if (!$this->field) {
            return true;
        }
        $value = $input->getParent()->value()[$this->field] ?? null;

        // case when value condition is not specified.
        if ($this->isEmpty($this->value)) {
            return $this->hasValue($value);
        }
        // when input value is equal to the value condition.
        if (is_string($this->value)) {
            return $this->value === $value;
        }
        // when input value is one of the value condition.
        if (is_array($this->value)) {
            return in_array($value, $this->value);
        }
        throw new InvalidArgumentException('value condition must be a string or an array.');
    }

    /**
     * use same name as Required class.
     * so that only one Required or RequiredIf can be used as filters.
     *
     * @return string
     */
    public function getFilterName(): string
    {
        return Required::class;
    }
}
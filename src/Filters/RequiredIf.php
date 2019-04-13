<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class RequiredIf extends AbstractFilter
{
    private $name;
    private $value;

    public function __construct($options = [])
    {
        $this->name = $options['field'] ?? null;
        $this->value = $options['value'] ?? null;
        $this->setPriority(FilterInterface::PRIORITY_REQUIRED_CHECK);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $isRequired = $this->checkCondition($input);
        if (!$isRequired) {
            return null;
        }
        $value = $input->value();
        if ($value === null || '' === (string)$value || empty($value)) {
            return $input->failed(Required::class);
        }
        return null;
    }

    private function checkCondition(ResultInterface $input): bool
    {
        if (!$this->name) {
            return true;
        }
        $value = $input->getParent()->value()[$this->name] ?? null;
        if ('' === (string)$this->value) {
            if ('' === (string)$value) {
                return false;
            }
            return true;
        }
        if (is_string($this->value)) {
            if ($this->value !== $value) {
                return false;
            }
            return true;
        }
        if (is_array($this->value)) {
            if (!in_array($value, $this->value)) {
                return false;
            }
            return true;
        }
        return true;
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
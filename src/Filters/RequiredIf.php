<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class RequiredIf extends AbstractFilter
{
    private $name;
    private $value;

    public function __construct($options = [])
    {
        $this->name = $options['if'] ?? null;
        $this->value = $options['value'] ?? null;
        $this->setPriority(FilterInterface::PRIORITY_REQUIRED_FILTERS);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $isRequired = $this->checkCondition($input);
        if (!$isRequired) {
            return null;
        }
        $value = $input->value();
        if ($value === null || '' === (string) $value) {
            return $this->failed($input);
        }
        return null;
    }

    private function checkCondition(ResultInterface $input): bool
    {
        if (!$this->name) {
            return true;
        }
        $value = $input->getParent()->value()[$this->name]?? null;
        if ('' === (string) $this->value) {
            if ('' === (string) $value) {
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
}
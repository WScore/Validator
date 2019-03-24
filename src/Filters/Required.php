<?php
namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class Required implements FilterInterface
{
    private $isRequired = true;
    private $name;
    private $value;

    public function __construct($options = [])
    {
        $this->isRequired = $options['isRequired'] ?? true;
        $this->name = $options['if'] ?? null;
        $this->value = $options['value'] ?? null;
    }

    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input, ResultInterface $allInputs): ?ResultInterface
    {
        $this->checkCondition($input);
        if (!$this->isRequired) {
            return null;
        }
        $value = $input->value();
        if ($value === '' || $value === null || empty($value)) {
            return $input->failed(__CLASS__, []);
        }
        return null;
    }

    private function checkCondition(ResultInterface $input)
    {
        if (!$this->name) {
            return;
        }
        $value = $input->getParent()->getChild($this->name)->value();
        if (is_string($this->value) && $this->value === $value) {
            $this->isRequired = true;
            return;
        }
        elseif (is_array($this->value) && in_array($value, $this->value)) {
            $this->isRequired = true;
            return;
        }
        $this->isRequired = false;
        return;
    }

    /**
     * returns the priority of the filter.
     * applies filters with smaller priority, first.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return FilterInterface::PRIORITY_REQUIRED_FILTERS;
    }

    /**
     * returns name of the filter;
     * validation can have only one filter with the same name.
     *
     * @return string
     */
    public function getFilterName(): string
    {
        return __CLASS__;
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validator\Filters;

use WScore\Validator\Interfaces\FilterInterface;
use WScore\Validator\Interfaces\ResultInterface;

abstract class AbstractFilter implements FilterInterface
{
    private $addType = FilterInterface::ADD_APPEND;

    private $isFilterForMultiple = false;

    public function getAddType(): string
    {
        return $this->addType;
    }

    /**
     * return true if the filter should applied in multiple
     * value (i.e. an array input) validation.
     *
     * @return bool
     */
    public function isFilterForMultiple(): bool
    {
        return $this->isFilterForMultiple;
    }

    /**
     * @param string $addType
     */
    protected function setAddType(string $addType): void
    {
        $this->addType = $addType;
    }

    /**
     * @param bool $isFilterForMultiple
     */
    protected function setIsFilterForMultiple(bool $isFilterForMultiple): void
    {
        $this->isFilterForMultiple = $isFilterForMultiple;
    }

    /**
     * @param ResultInterface $result
     * @param array $option
     * @param string $messages
     * @return ResultInterface
     */
    protected function failed(ResultInterface $result, $option = [], $messages = null)
    {
        return $result->failed(get_class($this), $option, $messages);
    }

    protected function hasValue($value): bool
    {
        return !$this->isEmpty($value);
    }

    protected function isEmpty($value): bool
    {
        if (is_object($value)) {
            return false;
        }
        if (is_array($value)) {
            if (empty($value)) {
                return true;
            }
            return false;
        }
        if ($value === null || (string) $value === '') {
            return true;
        }
        return false;
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validator\Filters;

use RuntimeException;
use WScore\Validator\Interfaces\ResultInterface;

final class ConfirmWith extends AbstractFilter
{
    const FIELD = 'field';
    const ERROR_MISSING = __CLASS__ . '::MISSING';
    const ERROR_DIFFER = __CLASS__ . '::DIFFER';

    /**
     * @var string
     */
    private $confirmWith;

    /**
     * @param array $option
     */
    public function __construct($option = [])
    {
        $this->confirmWith = $option[self::FIELD] ?? null;
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $parentInput = $input->getParent();
        if (!$parentInput) {
            throw new RuntimeException('must have parent input');
        }
        $confirmName = $this->confirmWith ?? $input->name() . '_confirmation';
        $confirmValue = $parentInput->value()[$confirmName] ?? '';
        return $this->confirmValue($input, $confirmValue);
    }

    /**
     * @param ResultInterface $input
     * @param string $confirmValue
     * @return ResultInterface|null
     */
    private function confirmValue(ResultInterface $input, string $confirmValue)
    {
        if ($confirmValue === (string) $input->getOriginalValue()) {
            return null;
        }
        if ($this->isEmpty($confirmValue)) {
            return $input->failed(self::ERROR_MISSING);
        }
        return $input->failed(self::ERROR_DIFFER);
    }
}
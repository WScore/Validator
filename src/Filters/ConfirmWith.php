<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use RuntimeException;
use WScore\Validation\Interfaces\ResultInterface;

class ConfirmWith extends AbstractFilter
{
    const MISSING = __CLASS__ . '::MISSING';
    const DIFFER = __CLASS__ . '::DIFFER';

    /**
     * @var string
     */
    private $confirmWith;

    /**
     * @param array $option
     */
    public function __construct($option = [])
    {
        $this->confirmWith = $option['with'] ?? null;
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $parentInput = $input->getParent();
        if (!$parentInput) {
            throw new RuntimeException('must have parent input');
        }
        $confirmName = $this->confirmWith ?? $input->name() . '_confirmation';
        $confirmValue = (string)$parentInput->value()[$confirmName] ?? '';
        return $this->confirmValue($input, $confirmValue);
    }

    /**
     * @param ResultInterface $input
     * @param string $confirmValue
     * @return ResultInterface|null
     */
    private function confirmValue(ResultInterface $input, string $confirmValue)
    {
        if ($confirmValue === (string)$input->value()) {
            return null;
        }
        if ($this->isEmpty($confirmValue)) {
            return $input->failed(self::MISSING);
        }
        return $input->failed(self::DIFFER);
    }
}
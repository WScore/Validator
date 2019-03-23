<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class ConfirmWith implements FilterInterface
{
    /**
     * @var string
     */
    private $confirmWith;

    /**
     * @param string $confirmWith
     */
    public function __construct($confirmWith = '')
    {
        $this->confirmWith = $confirmWith;
    }

    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input, ResultInterface $allInputs): ?ResultInterface
    {
        $confirmName = $this->confirmWith ?? $input->name() . '_confirmation';
        $confirmInput = $input->getParent()->getChild($confirmName);
        if (!$confirmInput) {
            return $input->failed('no input to confirm with: ' . $confirmName);
        }
        $confirmValue = $confirmInput->value();
        if ($confirmValue !== $input->value()) {
            return $input->failed('confirmation failed. ');
        }
        return null;
    }

    /**
     * returns the priority of the filter.
     * applies filters with smaller priority, first.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return FilterInterface::PRIORITY_VALIDATIONS;
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
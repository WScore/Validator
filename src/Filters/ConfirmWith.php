<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Result;

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
        $confirmValue = $input->getParent()->value()[$confirmName] ?? '';
        if ($confirmValue === $input->value()) {
            return null;
        }
        if ($this->empty($confirmValue)) {
            $confirmResult = new Result(null);
            $confirmResult->failed(Required::class);
            $input->getParent()->addResult($confirmResult, $confirmName);
            return $input->failed(__CLASS__);
        }
        return $input->failed(__CLASS__);
    }

    private function empty($string)
    {
        return '' !== (string) $string;
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
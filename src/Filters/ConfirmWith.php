<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Validators\Result;

class ConfirmWith extends AbstractFilter
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
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $confirmName = $this->confirmWith ?? $input->name() . '_confirmation';
        $confirmValue = $input->getParent()->value()[$confirmName] ?? '';
        if ($confirmValue === $input->value()) {
            return null;
        }
        if ($this->isEmpty($confirmValue)) {
            $confirmResult = new Result(null, null);
            $confirmResult->failed(Required::class);
            $input->getParent()->addResult($confirmResult, $confirmName);
            return $input->failed(__CLASS__);
        }
        return $input->failed(__CLASS__);
    }
}
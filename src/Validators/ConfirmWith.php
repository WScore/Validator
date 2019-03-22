<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Interfaces\ValidatorInterface;

class ConfirmWith implements ValidatorInterface
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
}
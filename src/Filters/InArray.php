<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class InArray extends AbstractValidator
{
    /**
     * @var array
     */
    private $inArray;

    /**
     * @var bool
     */
    private $strict = true;

    /**
     * @param array $inArray
     */
    public function __construct(array $inArray)
    {
        $this->inArray = $inArray;
        $this->setPriority(FilterInterface::PRIORITY_STRING_FILTERS);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if (in_array($value, $this->inArray, $this->strict)) {
            return null;
        } else {
            return $value;
        }
    }
}
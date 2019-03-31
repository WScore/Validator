<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class Required extends AbstractValidator
{
    public function __construct()
    {
        $this->setPriority(FilterInterface::PRIORITY_REQUIRED_FILTERS);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if ($value === '' || $value === null || empty($value)) {
            return $this->failed($input);
        }
        return null;
    }
}
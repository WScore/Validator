<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class ValidateInteger extends AbstractFilter
{
    use ValidateUtf8Trait;

    const INVALID_CHAR = __CLASS__ . '::INVALID_CHAR';
    const ARRAY_INPUT = __CLASS__ . '::ARRAY_INPUT';

    public function __construct()
    {
        $this->setPriority(FilterInterface::PRIORITY_FILTER_SANITIZE);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        if ($bad = $this->checkUtf8($input)) {
            return $bad;
        }
        $value = $input->value();
        if (!is_numeric($value)) {
            $input->setValue(null);
            return $input->failed(__CLASS__);
        }
        $input->setValue((int)$value);
        return null;
    }
}
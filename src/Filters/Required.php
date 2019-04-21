<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class Required extends AbstractFilter
{
    const NULLABLE = 'nullable';

    /**
     * @var bool
     */
    private $nullable = false;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->nullable = $options[self::NULLABLE] ?? false;
        $this->setPriority(FilterInterface::PRIORITY_REQUIRED_CHECK);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if (!$this->isEmpty($value)) {
            return null;
        }
        if ($this->nullable) {
            return $input;
        }
        return $input->failed(__CLASS__);
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class ValidateUtf8String extends AbstractFilter
{
    use ValidateUtf8Trait;

    const ERROR_INVALID_CHAR = __CLASS__ . '::INVALID_CHAR';
    const ERROR_ARRAY_INPUT = __CLASS__ . '::ARRAY_INPUT';
    const ERROR_INPUT_SIZE_MAX = __CLASS__ . '::INPUT_SIZE_MAX';

    /**
     * @var int
     */
    private $max;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->max = $options['max'] ?? 1028*1028; // 1MB
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        if ($bad = $this->checkUtf8($input, $this->max)) {
            return $bad;
        }

        return null;
    }
}
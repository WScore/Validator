<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class ValidateUtf8String extends AbstractFilter
{
    const INVALID_CHAR = __CLASS__ . '::INVALID_CHAR';
    const ARRAY_INPUT = __CLASS__ . '::ARRAY_INPUT';
    const INPUT_SIZE_MAX = __CLASS__ . '::INPUT_SIZE_MAX';

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
        $this->setPriority(FilterInterface::PRIORITY_FILTER_SANITIZE);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if (is_array($value)) {
            $input->setValue(null);
            return $input->failed(self::ARRAY_INPUT);
        }
        if (!mb_check_encoding($value, 'UTF-8')) {
            $input->setValue(null);
            return $input->failed(self::INVALID_CHAR);
        }
        if (strlen($value) > $this->max) {
            $input->setValue(null);
            return $input->failed(self::INPUT_SIZE_MAX, ['max' => $this->max]);
        }
        return null;
    }
}
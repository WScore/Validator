<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use InvalidArgumentException;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class StringCases extends AbstractFilter
{
    const TO_UPPER = 'upper';
    const TO_LOWER = 'lower';
    const UC_WORDS = 'uc_wordS';
    const UC_FIRST = 'uc_first';

    /**
     * @var string[]
     */
    private $functions = [
        self::TO_UPPER => 'strtoupper',
        self::TO_LOWER => 'strtolower',
        self::UC_WORDS => 'ucwords',
        self::UC_FIRST => 'ucfirst',
    ];

    /**
     * @var string[]
     */
    private $options;

    /**
     * list functions to operate in order, as;
     * [self::TO_LOWER, self::UC_WORDS]
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->setPriority(FilterInterface::PRIORITY_FILTER_MODIFIER);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        foreach ($this->options as $option) {
            if (!array_key_exists($option, $this->functions)) {
                throw new InvalidArgumentException('cannot find function for :' . $option);
            }
            $function = $this->functions[$option];
            $value = call_user_func($function, $value);
        }
        $input->setValue($value);

        return null;
    }
}

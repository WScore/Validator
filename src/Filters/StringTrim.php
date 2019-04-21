<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use InvalidArgumentException;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class StringTrim extends AbstractFilter
{
    const TRIM = 'trim';
    const LEFT_TRIM = 'ltrim';
    const RIGHT_TRIM = 'rtrim';

    private $functions = [
        self::TRIM, self::LEFT_TRIM, self::RIGHT_TRIM,
    ];

    /**
     * @var string
     */
    private $trim;

    /**
     * @var string|null
     */
    private $mask;

    /**
     * list functions to operate in order, as;
     * [self::TO_LOWER, self::UC_WORDS]
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->trim = $options['trim'] ?? self::TRIM;
        $this->mask = $options['mask'] ?? null;
        if (!in_array($this->trim, $this->functions)) {
            throw new InvalidArgumentException('No such trim function: ' . $this->trim);
        }
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        $function = $this->trim;
        if ($this->mask !== null) {
            $value = $function($value, $this->mask);
        } else {
            $value = $function($value);
        }
        $input->setValue($value);

        return null;
    }
}

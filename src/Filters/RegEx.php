<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use InvalidArgumentException;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class RegEx extends AbstractFilter
{
    const PATTERN = 'pattern';
    const MESSAGE = 'message';
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $message;

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->pattern = $options[self::PATTERN] ?? null;
        $this->message = $options[self::MESSAGE] ?? null;
        $this->setPriority(FilterInterface::PRIORITY_VALIDATIONS);
        if (!$this->pattern) {
            throw new InvalidArgumentException('pattern not set in Match filter');
        }
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $pattern = '/\\A' . $this->pattern . '\\z/us';
        $value = $input->value();
        if (preg_match($pattern, $value)) {
            return null;
        }
        return $input->failed(__CLASS__, [self::PATTERN => $pattern], $this->message);
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use InvalidArgumentException;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class RegEx extends AbstractFilter
{
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
        $this->pattern = $options['pattern'] ?? null;
        $this->message = $options['message'] ?? null;
        $this->setPriority(FilterInterface::PRIORITY_VALIDATIONS);
        if (!$this->pattern) {
            throw new InvalidArgumentException('pattern not set in Match filter');
        }
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $pattern = '/\\A' . $this->pattern . '\\z/us';
        $value = $input->value();
        if (preg_match($pattern, $value)) {
            return null;
        }
        return $input->failed(__CLASS__, ['pattern' => $pattern], $this->message);
    }

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return __CLASS__;
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use DateTimeImmutable;
use Exception;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class ValidateDateTime extends AbstractFilter
{
    use ValidateUtf8Trait;

    /**
     * @var string
     */
    private $format;

    /**
     * FilterDateTime constructor.
     * @param array $option
     */
    public function __construct($option = [])
    {
        $this->format = $option['format'] ?? null;
        $this->setPriority(FilterInterface::PRIORITY_FILTER_SANITIZE);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if ($this->isEmpty($value)) {
            return null;
        }
        if ($bad = $this->checkUtf8($input)) {
            return $bad;
        }
        try {
            $date = isset($this->format)
                ? DateTimeImmutable::createFromFormat($this->format, $value)
                : new DateTimeImmutable($value);
            $input->setValue($date);
        } catch (Exception $e) {
            $input->setValue(null);
            return $input->failed(__CLASS__);
        }
        return null;
    }
}
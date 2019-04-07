<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use DateTimeImmutable;
use Exception;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class ConvertDateTime extends AbstractFilter
{
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
        $this->setPriority(FilterInterface::PRIORITY_SECURITY_FILTERS);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        try {
            $date = isset($this->format)
                ? DateTimeImmutable::createFromFormat($this->format, $value)
                : new DateTimeImmutable($value);
            $input->setValue($date);
        } catch (Exception $e) {
            $input->failed(__CLASS__);
            $input->setValue(null);
            return $input;
        }
        return null;
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use InvalidArgumentException;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

/**
 * converts array input to a single value.
 *
 * $filter = new FilterArrayToValue([
 *    'fields' => ['y', 'm', 'd'],     // <- required!
 *    'format' => '{y}-{m}-{d}',       // <- sprintf format for field values.
 *    'implode' => '-',                // <- if no format, implode values with this char.
 * ]);
 */
class FilterArrayToValue extends AbstractFilter
{
    const MISSING_FIELD = __CLASS__ . '::MISSING_FIELD';

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $implode;

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->fields = $options['fields'] ?? [];
        $this->format = $options['format'] ?? null;
        $this->implode = $options['implode'] ?? '-';
        $this->setPriority(FilterInterface::PRIORITY_FILTER_PREPARE);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $this->checkFields();
        $value = $input->value();
        if ($this->isEmpty($value)) return null;
        if (!is_array($value)) return null;

        $value = $this->arrayToValue($input);
        $input->setValue($value);
        return null;
    }

    private function arrayToValue(ResultInterface $input): string
    {
        $value = $input->value();
        $replace = [];
        foreach ($this->fields as $field) {
            $replace[] = $value[$field] ?? '';
        }
        if (isset($this->format)) {
            return sprintf($this->format, ...$replace);
        }
        return implode($this->implode, $replace);
    }

    private function checkFields(): void
    {
        if (empty($this->fields)) {
            throw new InvalidArgumentException('must set fields');
        }
    }
}
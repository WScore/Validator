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
        $this->setPriority(FilterInterface::PRIORITY_PRE_FILTERS);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $this->checkFields();
        $value = $input->value();
        if (is_array($value)) {
            $value = $this->arrayToValue($value);
            $input->setValue($value);
        }
        return null;
    }

    private function arrayToValue(array $value): string
    {
        $replace = [];
        foreach ($this->fields as $field) {
            if (isset($value[$field])) {
                $replace[] = $value[$field];
            }
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
        if (!$this->format && !$this->implode) {
            throw new InvalidArgumentException('must set either format or implode');
        }
    }
}
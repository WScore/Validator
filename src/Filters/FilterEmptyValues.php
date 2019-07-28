<?php
declare(strict_types=1);

namespace WScore\Validator\Filters;

use InvalidArgumentException;
use WScore\Validator\Interfaces\FilterInterface;
use WScore\Validator\Interfaces\ResultInterface;

/**
 * removes any inputs that are null or empty string, or empty array.
 * use this filter for one-to-many forms to ignore empty rows.
 */
final class FilterEmptyValues extends AbstractFilter
{

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $values = $input->value();
        if ($this->isEmpty($values)) {
            $input->setValue(null);
        }
        if (!is_array($values)) {
            return null;
        }
        $values = $this->cleanUp($values);
        $input->setValue($values);

        return null;
    }

    private function cleanUp(array $values)
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $value = $this->cleanUp($value);
            }
            if ($this->isEmpty($value)) {
                unset($values[$key]);
            } else {
                $values[$key] = $value;
            }
        }
        return $values;
    }
}

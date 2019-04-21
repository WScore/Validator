<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class InArray extends AbstractFilter
{
    const CHOICES = 'choices';
    const REPLACE = 'replace';
    const STRICT = 'strict';

    /**
     * @var array
     */
    private $choices;

    /**
     * @var bool
     */
    private $strict = true;

    /**
     * @var bool
     */
    private $replace;

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->choices = $options[self::CHOICES] ?? [];
        $this->replace = $options[self::REPLACE] ?? null;
        $this->strict = $options[self::STRICT] ?? true;
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if (isset($this->replace)) {
            if (array_key_exists($value, $this->replace)) {
                $input->setValue($this->replace[$value]);
                return null;
            }
            return $input->failed(__CLASS__);
        }
        if (in_array($value, $this->choices, $this->strict)) {
            return null;
        } else {
            return $input->failed(__CLASS__);
        }
    }
}
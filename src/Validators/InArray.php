<?php
namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;

class InArray extends AbstractMultipleValidator
{
    /**
     * @var array
     */
    private $inArray;

    /**
     * @var bool
     */
    private $strict = true;

    /**
     * @param array $inArray
     */
    public function __construct(array $inArray)
    {
        $this->inArray = $inArray;
    }

    /**
     * @param string $value
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return string|null
     */
    public function validate($value, ResultInterface $input, ResultInterface $allInputs)
    {
        if (in_array($value, $this->inArray, $this->strict)) {
            return null;
        } else {
            return $value;
        }
    }
}
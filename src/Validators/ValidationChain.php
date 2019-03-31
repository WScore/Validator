<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;

class ValidationChain extends AbstractValidation
{
    /**
     * @var bool
     */
    private $multiple = false;

    /**
     * @param string|string[] $value
     * @return ResultInterface
     */
    private function validateSingle($value)
    {
        $result = new Result($value, $this->name);
        $this->prepareFilters();
        return $this->applyFilters($result);
    }

    /**
     * @param string[] $value
     * @return ResultInterface
     */
    private function validateMultiple($value)
    {
        $results = new ResultList($value, $this->name);
        $this->prepareFilters();
        $values = $results->value();
        foreach ($values as $key => $val) {
            $result = new Result($val, $key);
            $result = $this->applyFilters($result);
            $results->addResult($result, $key);
        }

        return $results;
    }

    /**
     * @param string|string[] $value
     * @return ResultInterface|null
     */
    public function verify($value)
    {
        if ($this->multiple) {
            $result = $this->validateMultiple($value);
        } else {
            $result = $this->validateSingle($value);
        }
        $result->finalize($this->message, $this->error_message);
        return $result;
    }

    /**
     * @param bool $multiple
     * @return ValidationChain
     */
    public function setMultiple(bool $multiple = true): ValidationChain
    {
        $this->multiple = $multiple;
        return $this;
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use WScore\Validation\Filters\Required;
use WScore\Validation\Interfaces\ResultInterface;

class ValidationChain extends AbstractValidation
{
    /**
     * @var bool
     */
    private $multiple = false;

    /**
     * @param string|string[] $value
     * @param string|null $name
     * @param ResultInterface $parentResult
     * @return ResultInterface
     */
    private function validateSingle($value, $name = null, $parentResult = null)
    {
        $result = new Result($value, $name);
        $result->setParent($parentResult);
        $result = $this->applyFilters($result);
        $result->finalize($this->message, $this->error_message);

        return $result;
    }

    /**
     * @param string[] $value
     * @param string|null $name
     * @param ResultInterface $parentResult
     * @return ResultInterface
     */
    private function validateMultiple($value, $name = null, $parentResult = null)
    {
        $results = new ResultList($value, $name);
        $results->setParent($parentResult);
        if ($this->hasFilter(Required::class)) {
            $required = $this->getFilter(Required::class);
            $this->removeFilter(Required::class);
        }
        $values = $results->value();
        foreach ($values as $key => $val) {
            if ('' === (string) $val) continue;
            $result = new Result($val, $key);
            $result = $this->applyFilters($result);
            $results->addResult($result, $key);
        }
        $results->finalize($this->message, $this->error_message);
        if (isset($required)) {
            $required->apply($results);
        }

        return $results;
    }

    /**
     * @param string|string[] $value
     * @return ResultInterface|ResultList
     */
    public function verify($value)
    {
        if ($this->multiple) {
            $result = $this->validateMultiple($value);
        } else {
            $result = $this->validateSingle($value);
        }
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

    /**
     * @param array|string $value
     * @param string|null $name
     * @param ResultInterface|null $parentResult
     * @return mixed|ResultInterface
     */
    public function callVerify($value, $name = null, ResultInterface $parentResult = null)
    {
        if ($this->multiple) {
            $result = $this->validateMultiple($value, $name, $parentResult);
        } else {
            $result = $this->validateSingle($value, $name, $parentResult);
        }
        return $result;
    }
}
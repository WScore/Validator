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
     * @param ResultInterface $parentResult
     * @return ResultInterface
     */
    private function validateSingle($value, $parentResult = null)
    {
        $result = new Result($value, $this->name);
        $result->setParent($parentResult);
        $result = $this->applyFilters($result);
        $result->finalize($this->message, $this->error_message);

        return $result;
    }

    /**
     * @param string[] $value
     * @param ResultInterface $parentResult
     * @return ResultInterface
     */
    private function validateMultiple($value, $parentResult = null)
    {
        $results = new ResultList($value, $this->name);
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
            $required->__invoke($results);
        }

        return $results;
    }

    /**
     * @param string|string[] $value
     * @param ResultInterface $parentResult
     * @return ResultInterface|ResultList
     */
    public function verify($value, ResultInterface $parentResult = null)
    {
        if ($this->multiple) {
            $result = $this->validateMultiple($value, $parentResult);
        } else {
            $result = $this->validateSingle($value, $parentResult);
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
}
<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Interfaces\ValidationInterface;

class ValidationChain implements ValidationInterface
{
    use ValidationTrait;

    /**
     * @param string|string[] $value
     * @return ResultInterface
     */
    public function initialize($value)
    {
        $result = new Result();
        $result->setValue($value);
        return $result;
    }

    /**
     * @param ResultInterface $result
     * @param ResultInterface $rootResults
     * @return ResultInterface
     */
    public function validate($result, $rootResults = null)
    {
        foreach ($this->filters as $filter) {
            if ($filter->__invoke($result, $rootResults)) {
                break;
            }
        }
        foreach ($this->validators as $name => $validator) {
            if ($result = $validator->__invoke($result, $rootResults)) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * @param string|array $value
     * @return ResultInterface|null
     */
    public function verify($value)
    {
        $result = $this->initialize($value);
        return $this->validate($result);
    }
}
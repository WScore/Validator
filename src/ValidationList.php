<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Interfaces\ValidationInterface;

class ValidationList implements ValidationInterface
{
    use ValidationTrait;

    /**
     * @var ValidationInterface[]
     */
    private $validations = [];

    /**
     * @var ResultList
     */
    private $results;

    /**
     * @param array $inputs
     * @return ResultList|ResultInterface
     */
    public function initialize($inputs)
    {
        $results = new ResultList();
        $results->setValue($inputs);
        foreach ($this->validations as $name => $validation) {
            $value = $inputs[$name] ?? null;
            $results->addResult($validation->initialize($value));
        }
        return $results;
    }

    /**
     * @param ResultInterface $result
     * @param ResultInterface $rootResults
     * @return ResultInterface
     */
    public function validate($result, $rootResults = null)
    {
        // prepare rootResults
        $rootResults = $rootResults ?? $result;

        // perform pre-filters on all inputs.
        foreach ($this->filters as $filter) {
            if ($result = $filter->__invoke($result, $rootResults)) {
                return $result;
            }
        }
        // perform children's validation.
        foreach ($this->validations as $name => $validation) {
            $value = $rootResults->getChild($name);
            $validation->validate($value, $rootResults);
        }
        // perform post-validation on all inputs.
        foreach ($this->validators as $name => $validator) {
            if ($result = $validator->__invoke($result, $rootResults)) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * @param $value
     * @return ResultInterface
     */
    public function verify($value)
    {
        $result = $this->initialize($value);
        return $this->validate($result);
    }
}
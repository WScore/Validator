<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Interfaces\ValidationInterface;

class ValidationList extends AbstractValidation
{
    /**
     * @var ValidationInterface[]
     */
    private $validations = [];

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
        
        // perform children's validation.
        foreach ($this->validations as $name => $validation) {
            $value = $rootResults->getChild($name);
            $validation->validate($value, $rootResults);
        }
        // perform post-validation on all inputs.
        $this->sortFilters();
        foreach ($this->filters as $name => $validator) {
            if ($result = $validator->__invoke($result, $rootResults)) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * @param array $value
     * @return ResultInterface
     */
    public function verify($value)
    {
        $result = $this->initialize($value);
        return $this->validate($result);
    }
}
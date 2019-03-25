<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;

class ValidationList extends AbstractValidation
{
    /**
     * @param array $inputs
     * @return ResultList|ResultInterface
     */
    public function initialize($inputs)
    {
        $results = new ResultList($this->message, $this->name);
        $results->setValue($inputs);
        foreach ($this->children as $name => $validation) {
            $value = $inputs[$name] ?? null;
            $results->addResult($validation->initialize($value), $name);
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
        foreach ($this->children as $name => $validation) {
            $value = $rootResults->getChild($name);
            $validation->validate($value, $rootResults);
        }
        // perform post-validation on all inputs.
        $this->prepareFilters();
        return $this->applyFilters($result, $rootResults);
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
<?php
namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;

class ValidationList extends AbstractValidation
{
    /**
     * @param array $inputs
     * @return ResultList|ResultInterface
     */
    public function initialize($inputs)
    {
        $results = new ResultList($this->message, $inputs, $this->name);
        foreach ($this->children as $name => $validation) {
            $value = $inputs[$name] ?? null;
            $results->addResult($validation->initialize($value), $name);
        }
        return $results;
    }

    /**
     * @param ResultInterface $result
     * @return ResultInterface
     */
    public function validate($result)
    {
        // prepare rootResults
        $rootResults = $rootResults ?? $result;

        // perform children's validation.
        foreach ($this->children as $name => $validation) {
            $value = $rootResults->getChild($name);
            $validation->validate($value);
        }
        // perform post-validation on all inputs.
        $this->prepareFilters();
        return $this->applyFilters($result);
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
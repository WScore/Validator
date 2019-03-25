<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;

class ValidationMultiple extends AbstractValidation
{
    /**
     * @param string[] $value
     * @return ResultInterface
     */
    public function initialize($value)
    {
        $results = new ResultList($this->message, $value, $this->name);
        foreach ($value as $key => $val) {
            $result = new Result($this->message, $val, $key);
            $results->addResult($result, $key);
        }
        return $results;
    }

    /**
     * @param ResultInterface $results
     * @param ResultInterface $rootResults
     * @return ResultInterface
     */
    public function validate($results, $rootResults = null)
    {
        $this->prepareFilters();
        foreach ($results->getChildren() as $result) {
            $this->applyFilters($result, $rootResults);
        }
        return $results;
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
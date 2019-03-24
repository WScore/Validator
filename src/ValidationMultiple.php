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
        $results = new ResultList($this->message);
        $results->setValue($value);
        foreach ($value as $key => $val) {
            $result = new Result($this->message);
            $result->setValue($val);
            $results->addResult($result, $key);
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
        $this->prepareFilters();
        foreach ($result->getChildren() as $result) {
            $this->applyFilters($result, $rootResults);
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
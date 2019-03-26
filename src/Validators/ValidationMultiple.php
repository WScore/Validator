<?php
namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;

/**
 * validates an array input.
 * applies same filters for all inputs in an array.
 *
 * Class ValidationMultiple
 * @package WScore\Validation\Validators
 */
class ValidationMultiple extends AbstractValidation
{
    /**
     * @param string[] $value
     * @return ResultInterface|ResultList
     */
    public function initialize($value)
    {
        $results = new ResultList($this->message, $value, $this->name);
        return $results;
    }

    /**
     * @param ResultInterface|ResultList $results
     * @return ResultInterface
     */
    public function validate($results)
    {
        $this->prepareFilters();
        $values = $results->value();
        foreach ($values as $key => $val) {
            $result = new Result($this->message, $val, $key);
            $result = $this->applyFilters($result);
            $results->addResult($result, $key);
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
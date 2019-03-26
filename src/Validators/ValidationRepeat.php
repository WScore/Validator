<?php
namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;

/**
 * validates an repeated validation for one-to-many type forms.
 *
 * TODO: test me!
 *
 * Class ValidationMultiple
 * @package WScore\Validation\Validators
 */
class ValidationRepeat extends AbstractValidation
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
     * @return ResultInterface
     */
    public function validate($results)
    {
        foreach ($results as $result) {
            foreach ($this->children as $name => $validation) {
                $validation->validate($result);
            }
        }
        $this->prepareFilters();
        foreach ($results->getChildren() as $result) {
            $this->applyFilters($result);
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
<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;

/**
 * validates an repeated validation for one-to-many type forms.
 *
 * Class ValidationMultiple
 * @package WScore\Validation\Validators
 */
class ValidationRepeat extends AbstractValidation
{
    /**
     * @param string[] $value
     * @return ResultInterface|ResultList
     */
    private function initialize($value)
    {
        $results = new ResultList($value, $this->name);
        return $results;
    }

    /**
     * @param ResultInterface|ResultList $results
     * @return ResultInterface
     */
    private function validate($results)
    {
        $values = $results->value();
        foreach ($values as $key => $val) {
            foreach ($this->children as $name => $validation) {
                $result = $validation->verify($val, $results);
                $results->addResult($result, $key);
            }
        }
        foreach ($results->getChildren() as $result) {
            $this->applyFilters($result);
        }
        return $results;
    }

    /**
     * @param string|array $value
     * @param ResultInterface|null $parentResult
     * @return ResultInterface|ResultList
     */
    public function verify($value, ResultInterface $parentResult = null)
    {
        $result = $this->initialize($value);
        $result->setParent($parentResult);
        $result = $this->validate($result);
        $result->finalize($this->message, $this->error_message);
        return $result;
    }
}
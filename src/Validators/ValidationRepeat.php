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
     * @param string|null $name
     * @return ResultInterface|ResultList
     */
    private function initialize($value, $name)
    {
        $results = new ResultList($value, $name);
        // apply pre-filters.
        foreach ($this->preFilters as $filter) {
            if ($returned = $filter->apply($results)) {
                break;
            }
        }
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
                $result = $validation->callVerify($val, $name, $results);
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
     * @return ResultInterface|ResultList
     */
    public function verify($value)
    {
        return $this->callVerify($value);
    }

    /**
     * @param array|string $value
     * @param string|null $name
     * @param ResultInterface|null $parentResult
     * @return mixed|ResultInterface|ResultList
     */
    public function callVerify($value, $name = null, ResultInterface $parentResult = null)
    {
        $result = $this->initialize($value, $name);
        $result->setParent($parentResult);
        $result = $this->validate($result);
        $result->finalize($this->message, $this->error_message);
        return $result;
    }
}
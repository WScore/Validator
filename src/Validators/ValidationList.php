<?php
namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

/**
 * validates a list of input, like form input.
 *
 * @package WScore\Validation\Validators
 */
class ValidationList extends AbstractValidation
{
    /**
     * @var FilterInterface[]
     */
    private $preFilters = [];

    public function addPreFilters(FilterInterface ...$filters): self
    {
        foreach ($filters as $filter) {
            $this->preFilters[$filter->getFilterName()] = $filter;
        }
        return $this;
    }

    /**
     * @param array $inputs
     * @return ResultList|ResultInterface
     */
    private function initialize($inputs)
    {
        $results = new ResultList($inputs, $this->name);
        return $results;
    }

    /**
     * @param ResultInterface|ResultList $results
     * @return ResultInterface
     */
    private function validate($results)
    {
        // apply pre-filters.
        foreach ($this->preFilters as $filter) {
            if ($returned = $filter->__invoke($results)) {
                break;
            }
        }
        // perform children's validation.
        $inputs = $results->value();
        foreach ($this->children as $name => $validation) {
            $value = $inputs[$name] ?? null;
            $result = $validation->verify($value);
            $results->addResult($result);
        }
        // perform post-validation on all inputs.
        $this->prepareFilters();
        return $this->applyFilters($results);
    }

    /**
     * @param array $value
     * @return ResultInterface|ResultList
     */
    public function verify($value)
    {
        $result = $this->initialize($value);
        $result = $this->validate($result);
        $result->finalize($this->message, $this->error_message);
        return $result;
    }
}
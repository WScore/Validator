<?php
namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

/**
 * validates a list of input, like form input.
 *
 * TODO: addPreFilter to perform filters before the main validation.
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
    public function initialize($inputs)
    {
        $results = new ResultList($this->message, $inputs, $this->name);
        return $results;
    }

    /**
     * @param ResultInterface|ResultList $results
     * @return ResultInterface
     */
    public function validate($results)
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
     * TODO: finalize result when finish validating.
     *
     * @param array $value
     * @return ResultInterface
     */
    public function verify($value)
    {
        $result = $this->initialize($value);
        $result = $this->validate($result);
        $result->finalize();
        return $result;
    }
}
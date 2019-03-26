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
        foreach ($this->children as $name => $validation) {
            $value = $inputs[$name] ?? null;
            $results->addResult($validation->initialize($value), $name);
        }
        return $results;
    }

    /**
     * @param ResultInterface $results
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
        foreach ($this->children as $name => $validation) {
            $result = $results->getChild($name);
            $validation->validate($result);
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
        return $this->validate($result);
    }
}
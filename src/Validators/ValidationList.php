<?php
declare(strict_types=1);

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

    /**
     * @param string $name
     * @param ValidationList $form
     * @return $this
     */
    public function addRepeatedForm(string $name, ValidationList $form)
    {
        $repeat = new ValidationRepeat($this->message);
        $repeat->add($name, $form);
        $this->add($name, $repeat);
        return $this;
    }

    /**
     * @param FilterInterface[] $filters
     * @return ValidationList
     */
    public function addPreFilters(FilterInterface ...$filters): self
    {
        foreach ($filters as $filter) {
            $this->preFilters[$filter->getFilterName()] = $filter;
        }
        return $this;
    }

    /**
     * @param array $inputs
     * @param string|null $name
     * @return ResultList|ResultInterface
     */
    private function initialize($inputs, $name = null)
    {
        $results = new ResultList($inputs, $name);
        return $results;
    }

    /**
     * @param ResultInterface|ResultList $results
     * @return ResultInterface|ResultList
     */
    private function validate($results)
    {
        // apply pre-filters.
        foreach ($this->preFilters as $filter) {
            if ($returned = $filter->apply($results)) {
                break;
            }
        }
        // perform children's validation.
        $inputs = $results->value();
        foreach ($this->children as $name => $validation) {
            $value = $inputs[$name] ?? null;
            $result = $validation->callVerify($value, $name, $results);
            $results->addResult($result);
        }
        // perform post-validation on all inputs.
        return $this->applyFilters($results);
    }

    /**
     * @param array $value
     * @return ResultInterface|ResultList
     */
    public function verify($value)
    {
        return $this->callVerify($value);
    }

    public function callVerify($value, $name = null, ResultInterface $parentResult = null)
    {
        $result = $this->initialize($value, $name);
        $result->setParent($parentResult);
        $result = $this->validate($result);
        $result->finalize($this->message, $this->error_message);
        return $result;
    }
}
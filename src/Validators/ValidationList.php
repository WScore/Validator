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
        $repeat = new ValidationRepeat($this->message, $name);
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
     * @return ResultList|ResultInterface
     */
    private function initialize($inputs)
    {
        $results = new ResultList($inputs, $this->name);
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
            if ($returned = $filter->__invoke($results)) {
                break;
            }
        }
        // perform children's validation.
        $inputs = $results->value();
        foreach ($this->children as $name => $validation) {
            $value = $inputs[$name] ?? null;
            $result = $validation->verify($value, $results);
            $results->addResult($result);
        }
        // perform post-validation on all inputs.
        return $this->applyFilters($results);
    }

    /**
     * @param array $value
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
<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\Locale\Messages;
use WScore\Validation\ValidatorBuilder;

/**
 * validates a list of input, like form input.
 *
 * @package WScore\Validation\Validators
 */
class ValidationList extends AbstractValidation
{
    /**
     * @var ValidatorBuilder
     */
    private $builder;

    /**
     * @param Messages $message
     * @param ValidatorBuilder|null $builder
     */
    public function __construct(Messages $message, ValidatorBuilder $builder = null)
    {
        parent::__construct($message);
        $this->builder = $builder;
    }

    /**
     * @param string $name
     * @param ValidationInterface|ValidationList $form
     * @param array $options
     * @return $this
     */
    public function addRepeatedForm(string $name, ValidationInterface $form, array $options = [])
    {
        $repeat = $this->builder->repeat($options);
        $repeat->add('0', $form);
        $this->add($name, $repeat);
        return $this;
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

    /**
     * @param array $inputs
     * @param string|null $name
     * @return ResultList|ResultInterface
     */
    private function initialize($inputs, $name = null)
    {
        $results = new ResultList($inputs, $name);
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
     * @return ResultInterface|ResultList
     */
    private function validate($results)
    {
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
}
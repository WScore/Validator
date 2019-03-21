<?php
namespace WScore\FormModel\Validation;

class ValidationList implements ValidationInterface
{
    use ValidationTrait;

    /**
     * @var ValidationInterface[]
     */
    private $validators = [];

    /**
     * @var ResultList
     */
    private $results;

    /**
     * @param array $inputs
     * @return ResultList|ResultInterface
     */
    public function initialize($inputs)
    {
        $results = new ResultList();
        $results->setValue($inputs);
        foreach ($this->validators as $name => $validator) {
            $value = $inputs[$name] ?? null;
            $results->addResult($validator->initialize($value));
        }
        return $results;
    }

    /**
     * @param ResultInterface $result
     * @param ResultInterface $rootResults
     * @return ResultInterface|null
     */
    public function validate($result, $rootResults = null)
    {
        $rootResults = $rootResults ?? $result;
        foreach ($this->validators as $name => $validator) {
            $value = $rootResults->getChild($name);
            $validator->validate($value, $rootResults);
        }
        return $result;
    }
}
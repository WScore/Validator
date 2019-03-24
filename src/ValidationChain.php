<?php
namespace WScore\Validation;

use WScore\Validation\Interfaces\ResultInterface;

class ValidationChain extends AbstractValidation
{
    /**
     * @var null|string
     */
    private $initialMessage = null;

    /**
     * @param string|string[] $value
     * @return ResultInterface
     */
    public function initialize($value)
    {
        $result = new Result($this->message);
        $result->setValue($value);
        return $result;
    }

    /**
     * @param ResultInterface $result
     * @param ResultInterface $rootResults
     * @return ResultInterface
     */
    public function validate($result, $rootResults = null)
    {
        $this->prepareFilters();
        return $this->applyFilters($result, $rootResults);
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

    /**
     * @param string $initialMessage
     * @return ValidationChain
     */
    public function setInitialMessage(string $initialMessage): self
    {
        $this->initialMessage = $initialMessage;
        return $this;
    }
}
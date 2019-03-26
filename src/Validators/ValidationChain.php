<?php
namespace WScore\Validation\Validators;

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
        $result = new Result($this->message, $value, $this->name);
        return $result;
    }

    /**
     * @param ResultInterface $result
     * @return ResultInterface
     */
    public function validate($result)
    {
        $this->prepareFilters();
        return $this->applyFilters($result);
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
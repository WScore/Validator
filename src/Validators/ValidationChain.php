<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;

class ValidationChain extends AbstractValidation
{
    /**
     * @param string|string[] $value
     * @param string|null $name
     * @param ResultInterface $parentResult
     * @return ResultInterface
     */
    private function validate($value, $name = null, $parentResult = null)
    {
        $result = new Result($value, $name);
        $result->setParent($parentResult);
        $result = $this->applyFilters($result);
        $result->finalize($this->message, $this->error_message);

        return $result;
    }

    /**
     * @param string|string[] $value
     * @return ResultInterface|ResultList
     */
    public function verify($value)
    {
        return $this->validate($value);
    }

    /**
     * @param array|string $value
     * @param string|null $name
     * @param ResultInterface|null $parentResult
     * @return mixed|ResultInterface
     */
    public function callVerify($value, $name = null, ResultInterface $parentResult = null)
    {
        return $this->validate($value, $name, $parentResult);
    }
}
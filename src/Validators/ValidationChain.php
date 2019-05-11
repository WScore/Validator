<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;

class ValidationChain extends AbstractValidation
{
    /**
     * @param string|string[] $value
     * @return ResultInterface
     */
    public function verify($value)
    {
        return $this->callVerify($value);
    }

    /**
     * @param array|string $value
     * @param string|null $name
     * @param ResultInterface|null $parentResult
     * @return ResultInterface
     */
    public function callVerify($value, $name = null, ResultInterface $parentResult = null)
    {
        $result = $this->initialize($value, $name);
        $result->setParent($parentResult);
        $result = $this->validate($result);
        $result->finalize($this->message, $this->error_message);
        return $result;
    }

    private function initialize($value, $name = null)
    {
        $result = new Result($value, $name);
        // apply pre-filters.
        foreach ($this->preFilters as $filter) {
            if ($returned = $filter->apply($result)) {
                break;
            }
        }
        return $result;
    }

    /**
     * @param ResultInterface $result
     * @return ResultInterface
     */
    private function validate(ResultInterface $result)
    {
        $result = $this->applyFilters($result);

        return $result;
    }
}
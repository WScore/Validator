<?php
namespace WScore\Validation\Validators;

class Result extends AbstractResult
{
    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @return  void
     */
    public function finalize()
    {
    }
}
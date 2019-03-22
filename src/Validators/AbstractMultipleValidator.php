<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\ResultInterface;
use WScore\Validation\Interfaces\ValidatorInterface;

abstract class AbstractMultipleValidator implements ValidatorInterface
{
    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input, ResultInterface $allInputs): ?ResultInterface
    {
        $value = $input->value();
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->validate($item, $input, $allInputs);
            }
        } else {
            $value = $this->validate($value, $input, $allInputs);
        }
        $input->setValue($value);

        return null;
    }

    /**
     * @param string $value
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return string|null
     */
    abstract protected function validate($value, ResultInterface $input, ResultInterface $allInputs);
}
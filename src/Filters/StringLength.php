<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\ResultInterface;

class StringLength extends AbstractFilter
{
    /**
     * @var null|int
     */
    private $max = null;

    /**
     * @var null|int
     */
    private $min = null;

    /**
     * @var null|int
     */
    private $length = null;

    /**
     * @param int|null $max
     * @return StringLength
     */
    public function setMax(int $max): StringLength
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param int|null $min
     * @return StringLength
     */
    public function setMin(int $min): StringLength
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param int|null $length
     * @return StringLength
     */
    public function setLength(int $length): StringLength
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @param ResultInterface $input
     * @param int $length
     * @return ResultInterface|null
     */
    private function checkLength(ResultInterface $input, int $length)
    {
        if ($this->length !== $length) {
            return $input->failed(__CLASS__, ['length' => $this->length]);
        }
        return null;
    }

    /**
     * @param ResultInterface $input
     * @param int $length
     * @return ResultInterface|null
     */
    private function checkMax(ResultInterface $input, int $length)
    {
        if ($this->max < $length) {
            return $input->failed(__CLASS__, ['max' => $this->max]);
        }
        return null;
    }

    /**
     * @param ResultInterface $input
     * @param int $length
     * @return ResultInterface|null
     */
    private function checkMin(ResultInterface $input, int $length)
    {
        if ($length < $this->min) {
            return $input->failed(__CLASS__, ['min' => $this->min]);
        }
        return null;
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        $length = mb_strlen($value);
        if ($this->length !== null) {
            return $this->checkLength($input, $length);
        }
        if ($this->max !== null) {
            return $this->checkMax($input, $length);
        }
        if ($this->min !== null) {
            return $this->checkMin($input, $length);
        }
        return null;
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\ResultInterface;

final class StringLength extends AbstractFilter
{
    const LENGTH = __CLASS__ . '::LENGTH';
    const MAX = __CLASS__ . '::MAX';
    const MIN = __CLASS__ . '::MIN';

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
     * @var string
     */
    private $message = null;

    public function __construct($options = [])
    {
        foreach ($options as $key => $value) {
            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function setMessage(string $message): StringLength
    {
        $this->message = $message;
        return $this;
    }

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
            return $input->failed(self::LENGTH, ['length' => $this->length], $this->message);
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
            return $input->failed(self::MAX, ['max' => $this->max], $this->message);
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
            return $input->failed(self::MIN, ['min' => $this->min], $this->message);
        }
        return null;
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        $length = mb_strlen($value);
        if ($this->length !== null) {
            $this->checkLength($input, $length);
        }
        if ($this->max !== null) {
            $this->checkMax($input, $length);
        }
        if ($this->min !== null) {
            $this->checkMin($input, $length);
        }
        return null;
    }
}
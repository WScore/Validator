<?php
declare(strict_types=1);

namespace WScore\Validator\Filters;

use InvalidArgumentException;
use WScore\Validator\Interfaces\ResultInterface;

final class ValidateMatch extends AbstractFilter
{
    use ValidateUtf8Trait;

    const TYPE = 'type';

    const IP = __CLASS__ . '::IP'; // IP address
    const EMAIL = __CLASS__ . '::EMAIL'; // email address
    const URL = __CLASS__ . '::URL'; // URL
    const MAC = __CLASS__ . '::MAC'; // MAC Address

    private $type2filter = [
        self::IP => FILTER_VALIDATE_IP,
        self::EMAIL => FILTER_VALIDATE_EMAIL,
        self::URL => FILTER_VALIDATE_URL,
        self::MAC => FILTER_VALIDATE_MAC,
    ];
    /**
     * @var string
     */
    private $type;

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->type = $options[self::TYPE] ?? null;
        if (!$this->type) {
            throw new InvalidArgumentException('type not set in Match filter');
        }
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        if ($bad = $this->checkUtf8($input)) {
            return $bad;
        }
        $value = $input->value();
        if ($this->isEmpty($value)) {
            return null;
        }
        if (!isset($this->type2filter[$this->type])) {
            throw new InvalidArgumentException('not such type: ' . $this->type);
        }
        $filter = $this->type2filter[$this->type];
        if (filter_var($value, $filter)) {
            return null;
        }
        return $input->failed($this->type);
    }
}
<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use InvalidArgumentException;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class Match extends AbstractFilter
{
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
     * @var string
     */
    private $message;

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->type = $options['type'] ?? null;
        $this->message = $options['message'] ?? null;
        $this->setPriority(FilterInterface::PRIORITY_VALIDATIONS);
        if (!$this->type) {
            throw new InvalidArgumentException('type not set in Match filter');
        }
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        $filter = $this->type2filter[$this->type];
        if (filter_var($value, $filter)) {
            return null;
        }
        return $input->failed($this->type, [], $this->message);
    }

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return __CLASS__;
    }
}
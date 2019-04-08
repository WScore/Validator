<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use InvalidArgumentException;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class Match extends AbstractFilter
{
    const IP = FILTER_VALIDATE_IP; // IP address
    const EMAIL = FILTER_VALIDATE_EMAIL; // email address
    const URL = FILTER_VALIDATE_URL; // URL
    const MAC = FILTER_VALIDATE_MAC; // MAC Address

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
        if (filter_var($value, $this->type)) {
            return null;
        }
        return $input->failed(__CLASS__, ['type' => $this->type], $this->message);
    }

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return __CLASS__;
    }
}
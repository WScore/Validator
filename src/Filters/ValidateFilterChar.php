<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use InvalidArgumentException;
use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class ValidateFilterChar extends AbstractFilter
{
    use ValidateUtf8Trait;

    const DIGITS_ONLY = 'digits';
    const AL_NUM_ONLY = 'al_num';
    const CODE_ONLY = 'code';

    private $patterns = [
        self::DIGITS_ONLY => '[^0-9]',
        self::AL_NUM_ONLY => '[^:alnum:]',
        self::CODE_ONLY => '[^[:alnum:_-]]',
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->type = $options['type'] ?? null;
        $this->setPriority(FilterInterface::PRIORITY_FILTER_SANITIZE);
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
        if (!array_key_exists($this->type, $this->patterns)) {
            throw new InvalidArgumentException('unknown type: ' . $this->type);
        }
        $value = $input->value();
        $value = preg_replace("/{$this->patterns[$this->type]}/", '', $value);
        $input->setValue((string)$value);
        return null;
    }
}
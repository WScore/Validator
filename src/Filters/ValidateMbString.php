<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

final class ValidateMbString extends AbstractFilter
{
    use ValidateUtf8Trait;

    const MB_HANKAKU = 'aKVs';
    const MB_ZENKAKU = 'AKVS';
    const MB_HAN_KANA = 'khs';
    const MB_ZEN_KANA = 'KV';
    const MB_HIRAGANA = 'HVcS';
    const MB_KATAKANA = 'KVCS';

    /**
     * @var int
     */
    private $max;

    /**
     * @var string
     */
    private $convertType;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->convertType = $options['type'] ?? self::MB_ZEN_KANA;
        $this->max = $options['max'] ?? 1028*1028; // 1MB
        $this->setPriority(FilterInterface::PRIORITY_FILTER_PREPARE);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function apply(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        if ($bad = $this->checkUtf8($input, $this->max)) {
            return $bad;
        }
        $value = mb_convert_kana($value, $this->convertType, 'UTF-8');
        $input->setValue($value);

        return null;
    }
}
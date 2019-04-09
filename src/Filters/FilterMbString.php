<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

/**
 * todo: this is converter, but should run at the beginning of the validation loop.
 */
class FilterMbString extends AbstractFilter
{
    const MB_HANKAKU = 'aKVs';
    const MB_ZENKAKU = 'AKVS';
    const MB_HAN_KANA = 'khs';
    const MB_ZEN_KANA = 'KV';
    const MB_HIRAGANA = 'HVcS';
    const MB_KATAKANA = 'KVCS';

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
        $this->setPriority(FilterInterface::PRIORITY_STRING_FILTERS);
    }

    /**
     * @param ResultInterface $input
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input): ?ResultInterface
    {
        $value = $input->value();
        $value = mb_convert_kana($value, $this->convertType, 'UTF-8');
        $input->setValue($value);

        return null;
    }

    /**
     * returns the priority of the filter.
     * applies filters with smaller priority, first.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return FilterInterface::PRIORITY_STRING_FILTERS;
    }

    /**
     * returns name of the filter;
     * validation can have only one filter with the same name.
     *
     * @return string
     */
    public function getFilterName(): string
    {
        return __CLASS__;
    }
}
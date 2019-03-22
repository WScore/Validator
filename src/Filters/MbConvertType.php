<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class MbConvertType implements FilterInterface
{
    const MB_HANKAKU = 'aKVs';
    const MB_ZENKAKU = 'AKVS';
    const MB_HAN_KANA = 'khs';
    const MB_HIRAGANA = 'HVcS';
    const MB_KATAKANA = 'KVCS';

    /**
     * @var string
     */
    private $convertType;

    public function __construct(string $convertType = self::MB_ZENKAKU)
    {
        $this->convertType = $convertType;
    }

    /**
     * @param ResultInterface $input
     * @param ResultInterface $allInputs
     * @return ResultInterface|null
     */
    public function __invoke(ResultInterface $input, ResultInterface $allInputs): ?ResultInterface
    {
        $value = $input->value();
        $value = mb_convert_kana($value, $this->convertType, 'UTF-8');
        $input->setValue($value);

        return null;
    }
}
<?php

/**
 * define filters for each validation type.
 */
return [
    'text' => [
        \WScore\Validation\Filters\FilterValidUtf8::class => true,
        \WScore\Validation\Filters\MbConvertType::class => \WScore\Validation\Filters\MbConvertType::MB_ZENKAKU,
        \WScore\Validation\Filters\DefaultValue::class => "",
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024*1024],
    ],
    'integer' => [
        \WScore\Validation\Filters\FilterValidUtf8::class => true,
        \WScore\Validation\Filters\MbConvertType::class => \WScore\Validation\Filters\MbConvertType::MB_HANKAKU,
        \WScore\Validation\Filters\DefaultValue::class => null,
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024],
    ],
    'date' => [
        \WScore\Validation\Filters\FilterValidUtf8::class => true,
        \WScore\Validation\Filters\MbConvertType::class => \WScore\Validation\Filters\MbConvertType::MB_HANKAKU,
        \WScore\Validation\Filters\DefaultValue::class => null,
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024],
    ],
    'datetime' => [],
    'YearMonth' => [],
];
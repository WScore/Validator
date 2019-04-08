<?php

/**
 * define filters for each validation type.
 */
return [
    'text' => [
        \WScore\Validation\Filters\ValidateUtf8String::class => true,
        \WScore\Validation\Filters\FilterMbString::class => \WScore\Validation\Filters\FilterMbString::MB_ZENKAKU,
        \WScore\Validation\Filters\DefaultValue::class => "",
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024*1024],
    ],
    'integer' => [
        \WScore\Validation\Filters\ValidateUtf8String::class => true,
        \WScore\Validation\Filters\FilterMbString::class => \WScore\Validation\Filters\FilterMbString::MB_HANKAKU,
        \WScore\Validation\Filters\DefaultValue::class => null,
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024],
    ],
    'date' => [
        \WScore\Validation\Filters\ValidateUtf8String::class => true,
        \WScore\Validation\Filters\FilterMbString::class => \WScore\Validation\Filters\FilterMbString::MB_HANKAKU,
        \WScore\Validation\Filters\DefaultValue::class => null,
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024],
    ],
    'datetime' => [],
    'YearMonth' => [],
];
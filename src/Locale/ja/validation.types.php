<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * define filters for each validation type.
 */
return [
    'text' => [
        \WScore\Validation\Filters\FilterMbString::class => ['type' => \WScore\Validation\Filters\FilterMbString::MB_ZENKAKU],
        \WScore\Validation\Filters\ValidateUtf8String::class,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => ''],
    ],
    'integer' => [
        \WScore\Validation\Filters\FilterMbString::class => ['type' => \WScore\Validation\Filters\FilterMbString::MB_HANKAKU],
        \WScore\Validation\Filters\ValidateInteger::class,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => null],
    ],
    'float' => [
        \WScore\Validation\Filters\FilterMbString::class => ['type' => \WScore\Validation\Filters\FilterMbString::MB_HANKAKU],
        \WScore\Validation\Filters\ValidateFloat::class,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => null],
    ],
    'date' => [
        \WScore\Validation\Filters\FilterMbString::class => ['type' => \WScore\Validation\Filters\FilterMbString::MB_HANKAKU],
        \WScore\Validation\Filters\ValidateDateTime::class,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => null],
    ],
    'email' => [
        \WScore\Validation\Filters\FilterMbString::class => ['type' => \WScore\Validation\Filters\FilterMbString::MB_HANKAKU],
        \WScore\Validation\Filters\ValidateUtf8String::class,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => ''],
        \WScore\Validation\Filters\Match::class => ['type' => \WScore\Validation\Filters\Match::EMAIL],
    ],
    'datetime' => [],
    'YearMonth' => [],
];
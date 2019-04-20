<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * define filters for each validation type.
 */
return [
    'text' => [
        \WScore\Validation\Filters\ValidateMbString::class => ['type' => \WScore\Validation\Filters\ValidateMbString::MB_ZENKAKU],
        \WScore\Validation\Filters\DefaultValue::class => ['default' => ''],
    ],
    'integer' => [
        \WScore\Validation\Filters\ValidateMbString::class => ['type' => \WScore\Validation\Filters\ValidateMbString::MB_HANKAKU],
        \WScore\Validation\Filters\ValidateInteger::class,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => null],
    ],
    'float' => [
        \WScore\Validation\Filters\ValidateMbString::class => ['type' => \WScore\Validation\Filters\ValidateMbString::MB_HANKAKU],
        \WScore\Validation\Filters\ValidateFloat::class,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => null],
    ],
    'date' => [
        \WScore\Validation\Filters\ValidateMbString::class => ['type' => \WScore\Validation\Filters\ValidateMbString::MB_HANKAKU],
        \WScore\Validation\Filters\ValidateDateTime::class,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => null],
    ],
    'email' => [
        \WScore\Validation\Filters\ValidateMbString::class => ['type' => \WScore\Validation\Filters\ValidateMbString::MB_HANKAKU],
        \WScore\Validation\Filters\DefaultValue::class => ['default' => ''],
        \WScore\Validation\Filters\Match::class => ['type' => \WScore\Validation\Filters\Match::EMAIL],
    ],
    'datetime' => [],
    'YearMonth' => [],
];
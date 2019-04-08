<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * define filters for each validation type.
 */
return [
    // raw type. no filters.
    'raw' => [],

    'text' => [
        \WScore\Validation\Filters\ValidateUtf8String::class => true,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => ''],
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024*1024],
    ],
    'integer' => [
        \WScore\Validation\Filters\ValidateUtf8String::class => true,
        \WScore\Validation\Filters\DefaultValue::class => ['default' => null],
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024],
    ],
    'date' => [],
    'datetime' => [],
    'YearMonth' => [],
];
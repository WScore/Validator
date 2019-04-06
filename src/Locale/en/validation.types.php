<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * define filters for each validation type.
 */
return [
    // raw type. no filters.
    'raw' => [],

    'text' => [
        \WScore\Validation\Filters\FilterValidUtf8::class => true,
        \WScore\Validation\Filters\DefaultValue::class => "",
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024*1024],
    ],
    'integer' => [
        \WScore\Validation\Filters\FilterValidUtf8::class => true,
        \WScore\Validation\Filters\DefaultValue::class => null,
        \WScore\Validation\Filters\StringLength::class => ['max' => 1024],
    ],
    'date' => [],
    'datetime' => [],
    'YearMonth' => [],
];
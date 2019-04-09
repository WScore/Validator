<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * define filters for each validation type.
 */
return [
    'text' => [
        \WScore\Validation\Filters\ValidateUtf8String::class,
        \WScore\Validation\Filters\DefaultEmpty::class,
    ],
    'integer' => [
        \WScore\Validation\Filters\ValidateInteger::class,
        \WScore\Validation\Filters\DefaultNull::class,
    ],
    'date' => [
        \WScore\Validation\Filters\ValidateDateTime::class,
        \WScore\Validation\Filters\DefaultNull::class,
    ],
    'datetime' => [],
    'YearMonth' => [],
];
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
    'float' => [
        \WScore\Validation\Filters\ValidateFloat::class,
        \WScore\Validation\Filters\DefaultNull::class,
    ],
    'date' => [
        \WScore\Validation\Filters\ValidateDateTime::class,
        \WScore\Validation\Filters\DefaultNull::class,
    ],
    'email' => [
        \WScore\Validation\Filters\ValidateUtf8String::class,
        \WScore\Validation\Filters\DefaultEmpty::class,
        \WScore\Validation\Filters\Match::class => ['type' => \WScore\Validation\Filters\Match::EMAIL],
    ],
    'digits' => [
        \WScore\Validation\Filters\ValidateFilterChar::class,
        \WScore\Validation\Filters\DefaultEmpty::class,
    ],
    'datetime' => [],
    'YearMonth' => [],
];
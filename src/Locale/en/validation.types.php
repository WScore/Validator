<?php

/**
 * define filters for each validation type.
 */

use WScore\Validation\Filters\DefaultValue;
use WScore\Validation\Filters\Match;
use WScore\Validation\Filters\ValidateDateTime;
use WScore\Validation\Filters\ValidateDigits;
use WScore\Validation\Filters\ValidateFloat;
use WScore\Validation\Filters\ValidateInteger;
use WScore\Validation\Filters\ValidateUtf8String;

return [
    'text' => [
        ValidateUtf8String::class,
        DefaultValue::class => ['default' => ''],
    ],
    'integer' => [
        ValidateInteger::class,
        DefaultValue::class => ['default' => null],
    ],
    'float' => [
        ValidateFloat::class,
        DefaultValue::class => ['default' => null],
    ],
    'date' => [
        ValidateDateTime::class,
        DefaultValue::class => ['default' => null],
    ],
    'email' => [
        ValidateUtf8String::class,
        DefaultValue::class => ['default' => ''],
        Match::class => ['type' => Match::EMAIL],
    ],
    'digits' => [
        ValidateDigits::class,
        DefaultValue::class => ['default' => ''],
    ],
    'datetime' => [],
    'YearMonth' => [],
];
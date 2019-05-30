<?php

/**
 * define filters for each validation type.
 */

use WScore\Validation\Filters\DefaultValue;
use WScore\Validation\Filters\ValidateLetterType;
use WScore\Validation\Filters\ValidateMatch;
use WScore\Validation\Filters\ValidateDateTime;
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
        ValidateMatch::class => ['type' => ValidateMatch::EMAIL],
        DefaultValue::class => ['default' => ''],
    ],
    'digits' => [
        ValidateLetterType::class => [ValidateLetterType::TYPE => ValidateLetterType::DIGITS_ONLY],
        DefaultValue::class => ['default' => ''],
    ],
    'URL' => [
        ValidateMatch::class => [ValidateMatch::TYPE => ValidateMatch::URL],
        DefaultValue::class => ['default' => ''],
    ],
    'datetime' => [],
    'YearMonth' => [],
];
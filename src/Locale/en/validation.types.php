<?php

/**
 * define filters for each validation type.
 */

use WScore\Validator\Filters\DefaultValue;
use WScore\Validator\Filters\ValidateLetterType;
use WScore\Validator\Filters\ValidateMatch;
use WScore\Validator\Filters\ValidateDateTime;
use WScore\Validator\Filters\ValidateFloat;
use WScore\Validator\Filters\ValidateInteger;
use WScore\Validator\Filters\ValidateUtf8String;

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
    'datetime' => [
        ValidateDateTime::class,
        DefaultValue::class => ['default' => null],
    ],
    'month' => [
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
];
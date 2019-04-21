<?php

/**
 * define filters for each validation type.
 */

use WScore\Validation\Filters\DefaultValue;
use WScore\Validation\Filters\ValidateMatch;
use WScore\Validation\Filters\ValidateDateTime;
use WScore\Validation\Filters\ValidateDigits;
use WScore\Validation\Filters\ValidateFloat;
use WScore\Validation\Filters\ValidateInteger;
use WScore\Validation\Filters\ValidateMbString;

return [
    'text' => [
        ValidateMbString::class => [ValidateMbString::TYPE => ValidateMbString::MB_ZENKAKU],
        DefaultValue::class => ['default' => ''],
    ],
    'integer' => [
        ValidateMbString::class => [ValidateMbString::TYPE => ValidateMbString::MB_HANKAKU],
        ValidateInteger::class,
        DefaultValue::class => ['default' => null],
    ],
    'float' => [
        ValidateMbString::class => [ValidateMbString::TYPE => ValidateMbString::MB_HANKAKU],
        ValidateFloat::class,
        DefaultValue::class => ['default' => null],
    ],
    'date' => [
        ValidateMbString::class => [ValidateMbString::TYPE => ValidateMbString::MB_HANKAKU],
        ValidateDateTime::class,
        DefaultValue::class => ['default' => null],
    ],
    'email' => [
        ValidateMbString::class => [ValidateMbString::TYPE => ValidateMbString::MB_HANKAKU],
        ValidateMatch::class => ['type' => ValidateMatch::EMAIL],
        DefaultValue::class => ['default' => ''],
    ],
    'digits' => [
        ValidateMbString::class => [ValidateMbString::TYPE => ValidateMbString::MB_HANKAKU],
        ValidateDigits::class,
        DefaultValue::class => ['default' => ''],
    ],
    'datetime' => [],
    'YearMonth' => [],
];
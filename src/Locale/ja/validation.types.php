<?php

/**
 * define filters for each validation type.
 */

use WScore\Validation\Filters\DefaultValue;
use WScore\Validation\Filters\Match;
use WScore\Validation\Filters\ValidateDateTime;
use WScore\Validation\Filters\ValidateFloat;
use WScore\Validation\Filters\ValidateInteger;
use WScore\Validation\Filters\ValidateMbString;

return [
    'text' => [
        ValidateMbString::class => ['type' => ValidateMbString::MB_ZENKAKU],
        DefaultValue::class => ['default' => ''],
    ],
    'integer' => [
        ValidateMbString::class => ['type' => ValidateMbString::MB_HANKAKU],
        ValidateInteger::class,
        DefaultValue::class => ['default' => null],
    ],
    'float' => [
        ValidateMbString::class => ['type' => ValidateMbString::MB_HANKAKU],
        ValidateFloat::class,
        DefaultValue::class => ['default' => null],
    ],
    'date' => [
        ValidateMbString::class => ['type' => ValidateMbString::MB_HANKAKU],
        ValidateDateTime::class,
        DefaultValue::class => ['default' => null],
    ],
    'email' => [
        ValidateMbString::class => ['type' => ValidateMbString::MB_HANKAKU],
        DefaultValue::class => ['default' => ''],
        Match::class => ['type' => Match::EMAIL],
    ],
    'datetime' => [],
    'YearMonth' => [],
];
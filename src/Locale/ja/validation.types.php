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
use WScore\Validator\Filters\ValidateMbString;

return [
    'text' => [
        ValidateMbString::class => [ValidateMbString::TYPE => ValidateMbString::MB_ZEN_KANA],
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
    'datetime' => [
        ValidateMbString::class => [ValidateMbString::TYPE => ValidateMbString::MB_HANKAKU],
        ValidateDateTime::class,
        DefaultValue::class => ['default' => null],
    ],
    'month' => [
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
        ValidateLetterType::class => [ValidateLetterType::TYPE => ValidateLetterType::DIGITS_ONLY],
        DefaultValue::class => ['default' => ''],
    ],
    'URL' => [
        ValidateMbString::class => [ValidateMbString::TYPE => ValidateMbString::MB_HANKAKU],
        ValidateMatch::class => [ValidateMatch::TYPE => ValidateMatch::URL],
        DefaultValue::class => ['default' => ''],
    ],
];
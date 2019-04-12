<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * define default error message for each filter.
 */
return [
    // a fall back error message.
    \WScore\Validation\Locale\Messages::class => "validation failed",

    // fail for invalid charset string.
    \WScore\Validation\Filters\ValidateUtf8String::INVALID_CHAR => 'The input is invalid UTF-8 character.',
    \WScore\Validation\Filters\ValidateUtf8String::ARRAY_INPUT => 'The input is an array. ',
    \WScore\Validation\Filters\ValidateInteger::class => 'The input is not a valid integer. ',
    \WScore\Validation\Filters\ValidateFloat::class => 'The input is not a valid float. ',
    \WScore\Validation\Filters\ValidateDateTime::class => 'Invalid DateTime input value.',

    // error messages for StringLength.
    \WScore\Validation\Filters\StringLength::LENGTH => "The input must be {length} characters.",
    \WScore\Validation\Filters\StringLength::MAX => "The input is more than {max} characters.",
    \WScore\Validation\Filters\StringLength::MIN => "The input is less than {min} characters.",

    // error messages for Match
    \WScore\Validation\Filters\Match::IP => 'The input is not a valid IP address.',
    \WScore\Validation\Filters\Match::EMAIL => 'The input is not a valid email address',
    \WScore\Validation\Filters\Match::URL => 'The input is not a valid URL',
    \WScore\Validation\Filters\Match::MAC => 'The input is not a valid MAC address',

    // required value.
    \WScore\Validation\Filters\Required::class => "The input field is required.",

    // ConfirmWith
    \WScore\Validation\Filters\ConfirmWith::MISSING => 'The field for confirmation is empty.',
    \WScore\Validation\Filters\ConfirmWith::DIFFER => 'The input differs from confirmation.',
];

/*
 * NOTE:
 *
 * The default error messages do not contain {name} as default.
 * When validating a single value, name may not present.
 * Because of this, the default error messages doe not contain {name}
 * as part of the messages.
 */
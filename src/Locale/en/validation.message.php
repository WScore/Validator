<?php

/**
 * define default error message for each filter.
 */

use WScore\Validation\Filters\ConfirmWith;
use WScore\Validation\Filters\Match;
use WScore\Validation\Filters\Required;
use WScore\Validation\Filters\StringLength;
use WScore\Validation\Filters\ValidateDateTime;
use WScore\Validation\Filters\ValidateFloat;
use WScore\Validation\Filters\ValidateInteger;
use WScore\Validation\Filters\ValidateUtf8String;
use WScore\Validation\Locale\Messages;

return [
    // a fall back error message.
    Messages::class => "validation failed",

    // fail for invalid charset string.
    ValidateUtf8String::INVALID_CHAR => 'The input is invalid UTF-8 character.',
    ValidateUtf8String::ARRAY_INPUT => 'The input is an array. ',
    ValidateInteger::class => 'The input is not a valid integer. ',
    ValidateFloat::class => 'The input is not a valid float. ',
    ValidateDateTime::class => 'Invalid DateTime input value.',

    // error messages for StringLength.
    StringLength::LENGTH => "The input must be {length} characters.",
    StringLength::MAX => "The input is more than {max} characters.",
    StringLength::MIN => "The input is less than {min} characters.",

    // error messages for Match
    Match::IP => 'The input is not a valid IP address.',
    Match::EMAIL => 'The input is not a valid email address',
    Match::URL => 'The input is not a valid URL',
    Match::MAC => 'The input is not a valid MAC address',

    // required value.
    Required::class => "The input field is required.",

    // ConfirmWith
    ConfirmWith::MISSING => 'The field for confirmation is empty.',
    ConfirmWith::DIFFER => 'The input differs from confirmation.',
];

/*
 * NOTE:
 *
 * The default error messages do not contain {name} as default.
 * When validating a single value, name may not present.
 * Because of this, the default error messages doe not contain {name}
 * as part of the messages.
 */
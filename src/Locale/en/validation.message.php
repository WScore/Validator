<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * define default error message for each filter.
 */
return [
    // a fall back error message.
    \WScore\Validation\Locale\Messages::class => "validation failed",

    // fail for invalid charset string.
    \WScore\Validation\Filters\FilterValidUtf8::INVALID_CHAR => 'The input is invalid UTF-8 character.',
    \WScore\Validation\Filters\FilterValidUtf8::ARRAY_INPUT => 'The input is an array. ',
    \WScore\Validation\Filters\FilterArrayToValue::MISSING_FIELD => 'The input is missing "{field}" field.',

    // error messages for Convert Filters.
    \WScore\Validation\Filters\ConvertDateTime::class => 'Invalid DateTime input value.',

    // error messages for StringLength.
    \WScore\Validation\Filters\StringLength::LENGTH => "The input must be {length} characters.",
    \WScore\Validation\Filters\StringLength::MAX => "The input is more than {max} characters.",
    \WScore\Validation\Filters\StringLength::MIN => "The input is less than {min} characters.",

    // required value.
    \WScore\Validation\Filters\Required::class => "The input field is required.",
];

/*
 * NOTE:
 *
 * The default error messages do not contain {name} as default.
 * When validating a single value, name may not present.
 * Because of this, the default error messages doe not contain {name}
 * as part of the messages.
 */
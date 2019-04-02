<?php
/** @noinspection ALL */

/**
 * define default error message for each filter.
 */
return [
    // a fall back error message.
    \WScore\Validation\Locale\Messages::class => "validation failed",

    // error messages for FilterInterfaces.
    \WScore\Validation\Filters\StringLength::LENGTH => "The input must be {length} characters.",
    \WScore\Validation\Filters\StringLength::MAX => "The input is more than {max} characters.",
    \WScore\Validation\Filters\StringLength::MIN => "The input is less than {min} characters.",
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
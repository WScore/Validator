<?php

/**
 * define default error message for each filter.
 */
return [
    // a fall back error message.
    \WScore\Validation\Locale\Messages::class => "validation failed",

    // error messages for FilterInterfaces.
    \WScore\Validation\Filters\StringLength::class => "string must be less than {max}",
    \WScore\Validation\Filters\Required::class => "required",
];
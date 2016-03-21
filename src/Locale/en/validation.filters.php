<?php

// define order of filterOptions when applying. order can be critical when
// modifying the string (such as capitalize before checking patterns).
//   rule => option
// if option is FALSE, the rule is skipped.

return array(
    // filterOptions (modifies the value)
    'type'        => null,       // type of filter, such as 'text', 'mail', etc.
    'message'     => false,
    'multiple'    => false,      // multiple value.
    'noNull'      => true,       // filters out NULL (\0) char from the value.
    'encoding'    => 'UTF-8',    // checks the encoding of value.
    'trim'        => true,       // trims value.
    'sanitize'    => false,      // done, kind of
    'datetime'    => false,      // check for valid datetime object
    'string'      => false,      // converts value to upper/lower/etc.
    'custom'      => false,      // custom filter #1
    'custom2'     => false,      // custom filter #2
    'custom3'     => false,      // custom filter #3
    'default'     => '',         // sets default if value is empty.
    // validators (only checks the value).
    'required'    => false,      // fails if value is empty.
    'requiredIf'  => false,      // required if other key has value
    'loopBreak'   => true,       // done, skip validations if value is empty.
    'code'        => false,
    'maxlength'   => false,
    'pattern'     => false,      // checks pattern with preg_match.
    'matches'     => false,      // preg_match with default types.
    'min'         => false,
    'max'         => false,
    'range'       => false,
    'checkdate'   => false,
    'sameWith'    => false,      // comparing with other field.
    'sameAs'      => false,
    'sameEmpty'   => false,
);
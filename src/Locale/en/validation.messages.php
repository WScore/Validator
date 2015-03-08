<?php
namespace WScore\Validation\Locale;

return array(
    0           => 'invalid input',         // general error message 
    'encoding'  => 'invalid encoding',      // specific messages for method
    'required'  => 'required item',
    'in'        => 'invalid choice',
    'sameAs'    => 'value not the same',
    'sameEmpty' => 'missing value to compare',
    'max'      => 'exceeds max value',
    'min'      => 'below min value',
    'matches'   => [                        // message for matches and parameter
        'number' => 'only numbers (0-9)',
        'int'    => 'not an integer',
        'float'  => 'not a floating number',
        'code'   => 'only alpha-numeric characters',
        'mail'   => 'not a valid mail address',
    ],
    '_type_' => [
        'mail'     => 'invalid mail format',
        'number'   => 'not a number',
        'integer'  => 'not an integer',
        'float'    => 'not a float',
        'date'     => 'invalid date',
        'datetime' => 'invalid date-time',
        'dateYM'   => 'invalid year-month',
        'time'     => 'invalid time',
        'timeHI'   => 'invalid hour-minute',
        'tel'      => 'invalid tel number',
    ],
);
<?php
namespace WScore\Validation\Locale;

return array(
    0           => 'invalid input',         // general error message 
    'encoding'  => 'invalid encoding',      // specific messages for method
    'required'  => 'required item',
    'choice'    => 'invalid choice',
    'sameAs'    => 'value not the same',
    'sameEmpty' => 'missing value to compare',
    'matches'   => [                        // message for matches and parameter
        'number' => 'only numbers (0-9)',
        'int'    => 'not an integer',
        'float'  => 'not a floating number',
        'code'   => 'only alpha-numeric characters',
        'mail'   => 'not a valid mail address',
    ],
);
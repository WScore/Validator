<?php

// filters for various types of input.

return array(
    'binary'   => [ 'noNull' => false, 'encoding' => false, 'mbConvert' => false, 'trim' => false ],
    'text'     => [],
    'mail'     => [ 'string' => 'lower', 'matches' => 'mail', 'sanitize' => 'mail' ],
    'number'   => [ 'matches' => 'number' ],
    'integer'  => [ 'matches' => 'int' ],
    'float'    => [ 'matches' => 'float' ],
    'date'     => [ 'multiple' => 'YMD', 'pattern' => '[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}' ],
    'dateYM'   => [ 'multiple' => 'YM',  'pattern' => '[0-9]{4}-[0-9]{1,2}' ],
    'datetime' => [ 'multiple' => 'datetime', 'pattern' => '[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}:[0-9]{2}' ],
    'time'     => [ 'multiple' => 'His', 'pattern' => '[0-9]{2}:[0-9]{2}:[0-9]{2}' ],
    'timeHi'   => [ 'multiple' => 'Hi',  'pattern' => '[0-9]{2}:[0-9]{2}' ],
    'tel'      => [ 'multiple' => 'tel', 'pattern' => '[-0-9()]*' ],
    'fax'      => [ 'multiple' => 'tel', 'pattern' => '[-0-9()]*' ],
);
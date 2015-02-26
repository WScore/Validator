<?php

// filters for various types of input.

return array(
    'binary'   => [ 'noNull' => false, 'encoding' => false, 'mbConvert' => false, 'trim' => false ],
    'text'     => [],
    'hiragana' => [ 'mbConvert' => 'hiragana', 'kanaType' => 'hiragana' ],
    'katakana' => [ 'mbConvert' => 'katakana', 'kanaType' => 'katakana' ],
    'mail'     => [ 'mbConvert' => 'hankaku', 'string' => 'lower', 'matches' => 'mail', 'sanitize' => 'mail' ],
    'number'   => [ 'mbConvert' => 'hankaku', 'matches' => 'number' ],
    'integer'  => [ 'sanitize'  => 'int', 'mbConvert' => 'hankaku', 'matches' => 'int', 'max' => PHP_INT_MAX ],
    'float'    => [ 'mbConvert' => 'hankaku', 'matches' => 'float' ],
    'date'     => [ 'datetime' => true, 'multiple' => 'YMD', 'mbConvert' => 'hankaku', 'pattern' => '[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}' ],
    'dateYM'   => [ 'multiple' => 'YM',  'mbConvert' => 'hankaku', 'pattern' => '[0-9]{4}-[0-9]{1,2}' ],
    'datetime' => [ 'datetime' => true, 'multiple' => 'datetime', 'mbConvert' => 'hankaku', 'pattern' => '[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}:[0-9]{2}' ],
    'time'     => [ 'datetime' => true, 'multiple' => 'His', 'mbConvert' => 'hankaku', 'pattern' => '[0-9]{2}:[0-9]{2}:[0-9]{2}' ],
    'timeHi'   => [ 'multiple' => 'Hi',  'mbConvert' => 'hankaku', 'pattern' => '[0-9]{2}:[0-9]{2}' ],
    'tel'      => [ 'multiple' => 'tel', 'mbConvert' => 'hankaku', 'pattern' => '[-0-9()]*' ],
    'fax'      => [ 'multiple' => 'tel', 'mbConvert' => 'hankaku', 'pattern' => '[-0-9()]*' ],
);
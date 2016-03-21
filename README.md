Validation
==========

A validation component, optimized multi-byte (i.e. Japanese language) support.

Features includes, such as:

*   works well with code completion.
*   multiple values combined to a single value (ex: bd_y, bd_m, bd_d to bd).
*   preset order of rules to apply. essential to handle Japanese characters.
*   easy to code logic.


### License

MIT License

### Installation

Use composer. only dev-master is available...

```json
"require": {
    "wscore/validation": "dev-master"
}
```


Simple Usage
------------

This package **almost** works like this.

### factory object

Use ```ValidationFactory``` to start validating an array.
For instance, 

```php
use \WScore\Validation\ValidationFactory;

$factory = new ValidationFactory();    // use English rules and messages.
$factory = new ValidationFactory('ja); // use Japanese rules and messages.
```

to validate an array, 

```php
$input = $factory->on($_POST);
$input->asText('name');
$input->asInteger('age');
$input->asDate('bate');
if($v->fails()) {
   $messages = $input->messages();
}
$values = $input->get();
```


### validation rule

The ```as{Type}($name)``` method sets default rules for the input ```$name```. Use method chaing to add more rules. 

```php
$input = $factory->on( $_POST );   // get validator.
$input->asText('name')->required() );
$input->asMail('mail')->required()->sameWith('mail2'));
$found = $input->get(); // [ 'name' => some name... ]
if( $input->fails() ) {
    $onlyGoodData    = $input->getSafe();
    $containsBadData = $input->get();
} else {
    $onlyGoodData    = $input->get();
}
```

Check if the validation failes or not using ```fails()```
 method (or ```passess()``` method).

To retrieve the validated (or unvalidated) values
 by ```get()``` method, which returns all the values
 (validated and invalidated values).

To retrieve __only the validated values__,
 use ```getSafe()``` method.


### validating a single value

##### rewrite-this-section.

Use ```verify``` method to validate a single value.

```php
$name  = $input->verify( 'WScore', Rules::text()->string('lower') ); // returns 'wscore'
if( false === $input->verify( 'Bad', Rules::int() ) { // returns false
    echo $input->result()->message(); // echo 'not an integer';
}
```


Advanced Features
-----------------

### validating array as input

Validating an array of data is easy. When the validation fails,
 it returns the error message as array.

```php
$input->source( array( 'list' => [ '1', '2', 'bad', '4' ] ) );
$input->asInteger('list');
if( !$input->passes() ) {
    $values = $validation->get('list');
    $goods  = $validation->getSafe();
    $errors = $validation->message();
}
/*
 * $values = [ '1', '2', 'bad', '4' ];
 * $goods  = array( 'list' => [ '1', '2', '4' ] );
 * $errors = array( 'list' => [ 2 => 'not an integer' ] );
 */
```


### multiple inputs

to treat separate input fields as one input, such as date. 

```php
$input->source( [ 'bd_y' => '2001', 'bd_m' => '09', 'bd_d' => '25' ] );
echo $validation->is( 'bd', Rules::date() ); // 2001-09-25
```

use ```multiple``` rules to construct own multiple inputs as,

```php
$input->asText('ranges')->multiple( [
    'suffix' => 'y1,m1,y2,m2',
    'format' => '%04d/%02d - %04d/%02d'
] );
```

where ```suffix``` lists the postfix for the inputs,
and ```format``` is the format string using sprintf.


### sameWith to compare values

for password or email validation with two input fields 
to compare each other. 

```php
$input->source([ 'text1' => '123ABC', 'text2' => '123abc' ] );
echo $validation->asText('text1')
	->string('lower')
	->sameWith('text2') ); // 123abc
```

Please note that the actual input strings are different.
They become identical after lowering the strings.


### order of filter

some filter must be applied in certain order... 

```php
echo $validate->verify( 'ABC', Rules::text()->pattern('[a-c]*')->string('lower'); // 'abc'
## should lower the string first, then check for pattern...
```


### custom validation

Use a closure as custom validation filter.

```php
/**
 * @param ValueTO $v
 */
$filter = function( $v ) {
    $val = $v->getValue();
    $val .= ':customized!';
    $v->setValue( $val );
    $v->setError(__METHOD__);
    $v->setMessage('Closure with Error');
};
$input->asText('test')->addCustom( 'myFilter', $filter );
```

You cannot pass parameter (the closure is the parameter).
argument is the ValueTO object which can be used to handle
error and messages.

setting error with, well, actually, any string,
but ```__METHOD__``` maybe helpful. this will break the
filter loop, i.e. no filter will be evaluated.



Predefined Messages
-------------------

Error message is determined as follows:

1.   message to specify by message rule,
2.   method and parameter specific message,
3.   method specific message,
4.   type specific message, then,
5.   general message

### example 1) message to specify by message rule

for tailored message, use ```message``` method to set its messag.e

```php
$validate->verify( '', $rule('text')->required()->message('Oops!') );
echo $validate->result()->message(); // 'Oops!'
```

### example 2) method and parameter specific message

filter, ```matches``` has its message based on the parameter. 

```php
$validate->verify( '', Rules::text()->required()->matches('code') );
echo $validate->result()->message(); // 'only alpha-numeric characters'
```

### example 3 ) method specific message

filters such as ```required``` and ```sameWith``` has message.
And lastly, there is a generic message for general errors. 

```php
$validate->verify( '', $rule('text')->required() );
echo $validate->result()->message(); // 'required input'
```

### example 4) type specific message

```php
$validate->verify( '', Rules::date()->required() );
echo $validate->result()->message(); // 'invalid date'
```

### example 5) general message

uses generic message, if all of the above rules fails.

```php
$validate->verify( '123', Rules::text()->pattern('[abc]') );
echo $validate->result()->message(); // 'invalid input'
```

Predefined Types
----------------

todo: to-be-written

*   text
*   mail
*   number
*   integer
*   float
*   date
*   dateYM
*   datetime
*   time
*   timeHi
*   tel

Predefined Filters
------------------

todo: to-be-write

*   message
*   multiple
*   noNull
*   encoding
*   mbConvert (Ja only)
*   trim
*   sanitize
*   string
*   default
*   required
*   loopBreak
*   code
*   maxlength
*   pattern
*   matches
*   kanaType (ja only)
*   etc.


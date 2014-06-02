WScore.Validation
=================

A simple validation component with many multi-byte support.

Easy to use, enjoyable to write code,
lots of default error messages,
lots of pre-defined validation types, and
works great with multi-byte characters (Japanese that is).

Others are:

*   preset order of rules to apply. essential to handle Japanese characters.
*   possigle to validate an array as input.
*   multiple values combined to a single value (ex: bd_y, bd_m, bd_d to bd).
*   easy to code logic.


### License

MIT License

### Installation

use composer. only dev-master is available...

```json
"require": {
    "wscore/validation": "dev-master"
}
```


Simple Usage
------------

This package **almost** works like this.

### factory object

use ```Factory``` to construct validation object.
set the data to validate using ```source``` method. For verifying 
form input, the source would be ```$_POST``` as in the example. 

```php
use \WScore\Validation\Factory;
use \WScore\Validation\Rules;

Factory::setLocale('ja');       // use Japanese rules and messages.
$input = Factory::input();      // get validator.
// $input->source( $_POST );    // default is to validate post input.
```


### validating data

The ```is``` method validates and returns the found value, or returns
false if fails to validate.

Use static class, ```Rules```, to compose a rules (ala Facade).

```php
$input( 'name', Rules::text()->required() );
$input( 'mail', Rules::mail()->required() );
$found = $input->get(); // [ 'name' => some name... ]
```

When the validation process is completed, retrieve the validated
value by ```get``` method.

#### example code

Because ```is``` returns false when validation fails, it is easy to
write a logic based the returned value.


```php
// check if name or nickname is set
if( !$input->is( 'name', Rules::text() ) ) {
    if( !$input->is( 'nickname', Rules::text() ) ) {
        $input->isError( 'name', 'requires name or nickname' );
    }
}

// check mail with confirmation
$input->is( 'mail', Rules::mail()->sameWith( 'mail2' )->required() );

// check value of input, and do more stuff.
$status = $input->is( 'status', Rules::int()->in( '1', '2', '3' )->required()->message('must be 1-3.') );
if( $status == '1' ) { // add some message?!
    $input->setValue( 'notice', 'how do you like it?' );
} elseif( false === $status ) { // maybe get some reasons...
    echo $input->is('reason', Rules::text()->required() );
}

if( $input->fails() ) {
    $onlyGoodData    = $input->getSafe();
    $containsBadData = $input->get();
    $message         = $input->message();
} else {
    $goodData = $input->get();
}
```

Please note that ```get``` method may return values that are not validated.


### Validating a single value

use ```verify``` method to validate a single value. 

```php
$name  = $input->verify( 'WScore', Rules::text()->string('lower') ); // returns 'wscore'
if( false === $input->verify( 'Bad', Rules::int() ) { // returns false
    echo $input->result()->message(); // echo 'not an integer';
}
```


Advanced Features
-----------------

### validating array as input

validation on array is easy. so is the error message. 

```php
$input->source( array( 'list' => [ '1', '2', 'bad', '4' ] ) );

if( !$input->is( 'list', Rules::int() ) ) {
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
Rules::text()->multiple( [
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
echo $validation->is( 'text1', Rules::text()->string('lower')->sameWith('text2') ); // 123abc
```


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
Rules::text()->addCustom( 'myFilter', $filter );
Rules::text()->custom( $filter );
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


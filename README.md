WScore.Validation
=================

A simple validation component with many multi-byte support.

Easy to use, enjoyable to write code,
lots of default error messages,
lots of pre-defined validation types, and
works great with multi-byte characters (Japanese that is).

Others are:

*   preset order of rules to apply. essential to handle Japanese characters.
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


Simple Usage That Should Be
---------------------------

### validating an input data

This package **almost** works like this.
```is``` method returns the found value, or false if fails to validate. 
This makes it easy to write a simple logic based the returned value. 

```php
use \WScore\Validation\Factory;
use \WScore\Validation\Rules;

Factory::setLocale('ja');          // use Japanese rules and messages.
$input = Factory::getValidator();  // get validator.
$input->setSource( $_POST );       // validating post input.

// check if name or nickname is set
if( !$input->is( 'name', Rules::text() ) ) {
    if( !$input->is( 'nickname', Rules::text() ) ) {
        $input->isError( 'name', 'requires name or nickname' );
    }
}

// check mail with confirmation
$input->is( 'mail', Rules::mail()->sameAs( 'mail2' )->required() );

// check value of input, and...
$status = $input->is( 'status', Rules::int()->in( '1', '2', '3' )->required() );
if( $status == '1' ) { // add some message?!
    $input->setValue( 'notice', 'how do you like it?' );
}

if( $input->fails() ) {
    $badData = $input->get();
    $message = $input->message();
} else {
    $goodData = $input->get();
}
```

### validating a single value

use ```verify``` method to validate a single value. 

```php
$name  = $input->verify( 'WScore', Rules::text()->string('lower') ); // returns 'wscore'
if( false === $input->verify( 'Bad', Rules::int() ) { // returns false
    echo $input->result()->message(); // echo 'not an integer';
}
```


Advanced Features
-----------------

### Validating Array Input

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


### Multiple inputs

to treat separate input fields as one input, such as date. 

```php
$input->source( [ 'bd_y' => '2001', 'bd_m' => '09', 'bd_d' => '25' ] );
echo $validation->is( 'bd', Rules::date() ); // 2001-09-25
```

### SameWith to compare values

for password or email validation with two input fields 
to compare each other. 

```php
$input = array( 'text1' => '123ABC', 'text2' => '123abc' );
echo $validation->push( 'bd', Rules::text()->string('lower')->sameWith('text2') ); // 123abc
```


### Order of filter

some filter must be applied in certain order... 

```php
echo $validate->is( 'ABC', Rules::text()->pattern('[a-c]*')->string('lower'); // 'abc'
## should lower the string first, then check for pattern...
```

### Many predefined error messages

some filter have own error messages, 
that are: required, encoding, sameAs, and sameEmpty. 
They are defined in Message class, and to be i18n ready in some future.

```php
$validate->is( '', $rule('text')->required() );
echo $validate->getMessage(); // 'required input'
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
*   etc.

Predefined Filters
------------------

todo: to-be-write

*   multiple
*   noNull
*   encoding
*   mbConvert
*   trim
*   etc.


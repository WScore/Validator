WScore.Validation
=================

Validation and filtration for values such as form input.

*   filteration:
    may change the input value. For instace, string:lower filter may change alphabets to lower character.
*   validation:
    will only validates the value (no changes to the value).

filters always should applied before the validators.
also the order of filters is important.

Simple Usage
------------

```php
$validate = \WScore\Validation\Validate::factory();
$rule     = new \WScore\Validation\Rule();

// basic validation by type

$value = 'a text';
echo $validate->is( 'a text', $rule('text') ); // 'a text'
echo $validate->is( 'a text', $rule('int') ); // false
echo $validate->is( 'a text', $rule('text')->string('upper') ); // 'A TEXT'

// validation and getting errors

$value = '';
echo $validate->is( $value, $rule('text') ); // ''
echo $validate->is( $value, $rule('text')->required() ); // false
echo $validate->isError(); // true
echo $validate->message(); // 'required field'
```

Objects
-------

*   Validate object:
    filters and validates a single value.

*   Validation object:
    filters and validates against an array of inputs, such as $_POST.
    Validation has multiple filter and sameAs validator which are not present in Validate.

*   Rules object:
    has predefined filters and validators to apply for several types of input.

More Example
------------

validation on input values.

```php
$validation = Validation::factory();

$validation->source( $_POST );
$validation->push( 'name', 'text' );
$validation->push( 'age',  'number' );
$validation->pushValue( 'status', '1' );

$values = $validation->pop();     // returns all values including invalid ones.
$goods  = $validation->popSafe(); // returns only the valid values.
$errors = $validation->popError();// get error messages.
```

Why Yet-Another Validation Component?
-------------------------------------

There are several reasons why I continued developing this Validation components. 

###Validating Array Input

validation on array is easy. so is the error message. 

```php
$inputs = array( 'list' => [ '1', '2', 'bad', '4' ] );
$validation->source( $inputs );

if( !$validation->push( 'list', 'int' ) ) {
    $values = $validate->pop();
    $errors = $validate->popError();
}
/*
 * $values = array( 'list' => [ '1', '2', 'bad', '4' ] );
 * $errors = array( 'list' => [ 3 => 'invalid input' ] );
 */
```

###Order of filter

some filter must be applied in certain order... 

```php
$validate->is( 'ABC', $rule('text')->pattern( '[a-c]*' )->string( 'lower' );
## should lower the string first, then check for pattern...
```

###Some predefined error messages

some filter have own error messages, 
that are: required, encoding, sameAs, and sameEmpty. 
They are defined in Message class, and to be i18n ready in some future.

```php
$input = array( 'none' => '' );
$validation->push( 'none', $rule('text')->required() );
$validation->popError(); // [ 'none' => 'required input' ]
```

###Multiple inputs

```php
$input = array( 'bd_y' => '2001', 'bd_m' => '09', 'bd_d' => '25' );
echo $validation->push( 'bd', $rule('date' ) ); // 2001-09-25
```

###SameWith to compare values

```php
$input = array( 'text1' => '123ABC', 'text2' => '123abc' );
echo $validation->push( 'bd', $rule('text')->string('lower')->sameWith('text2') ); // 123abc
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
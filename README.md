WScore.Validation
=================

Validation and filtration for values such as form input.

###some thought on filteration and validation.

*   filteration:
    may change the input value. For instace, string:lower filter may change alphabets to lower character.
*   validation:
    will only validates the value (no changes to the value).

filters always should applied before the validators.

also the order of filters is important.

Objects
-------

*   Validate object:
    filters and validates a single value.

*   Validation object:
    filters and validates against a set of inputs, such as $_POST.
    Validation has multiple filter and sameAs validator which are not present in Validate.

*   Rules object:
    has predefined filters and validators to apply for several types of input.

Example
-------

creating objects.

```php
$validate   = include( 'path/to/scripts/validate.php' );
$validation = include( 'path/to/scripts/validation.php' );
```

validate simple values.

```php
if( !$value = $validate->is( '123a', 'number' ) ) {
    echo 'not a number';
}
if( $value = $validate->is( 'abcABC', 'text|string:lower|pattern' ) ) {
    echo 'lower alphabets: ' . $value;
}
```

validation on input values.

```php
$validation->source( $_POST );
$validation->push( 'name', 'text' );
$validation->push( 'age',  'number' );
$validation->pushValue( 'status', '1' );
$values = $validation->pop();     // returns all values including invalid ones.
$goods  = $validation->popSafe(); // returns only the valid values.
$errors = $validation->popError();// get error messages.
```

Validating Array Input
----------------------

```php
$inputs = array( '1', '2', 'bad', '4' );
if( !$validate->is( $input, 'int' ) ) {
    $values = $validate->value;
    $errors = $validate->err_msg;
}
/*
 * $values = array( '1', '2', 'bad', '4' );
 * $errors = array( '3' => 'bad' );
 */
```

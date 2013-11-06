WScore.Validation
=================

Validation and filtration for values such as form input.

*   filteration:
    may change the input value. For instace, string:lower filter may change alphabets to lower character.
*   validation:
    will only validates the value (no changes to the value).

filters always should applied before the validators.
also the order of filters is important.

Usage (under refactoring)
-------------------------

```php
$validate = include( 'path/to/scripts/validate.php' );
$rule     = new \WScore\Validation\Rule();

// basic validation by type

$value = 'a text';
echo $validate->is( $value, $rule('text') ); // 'a text'
echo $validate->is( $value, $rule('int') ); // false

// validation and getting errors

$value = '';
echo $validate->is( $value, $rule('text') ); // ''
echo $validate->is( $value, $rule('text')->required() ); // false
echo $validate->isError(); // true
echo $validate->message(); // 'required field'

// another way to get rules
use WScore\Validation\Rule;
$value = '3';
echo $validate->is( $value, Rule::make( 'number|required|max:4' ) ); // '3'

```


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

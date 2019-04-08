Validation
==========

A validation component for,

- a single value as well as arrays, 
- nested form inputs, 
- one-to-many form inputs, and
- multi-byte support (i.e. Japanese), 


### License

MIT License

### Installation

t.b.w. 

Simple Usage
------------

### Validating a Value

Create a `ValidationBuilder` instance, then get validator like;

```php
$vb = new ValidatorBuilder();
$validator = $vb->text([
    'filters' => [
        StringLength::class => ['max' => 12],
    ],
]);
$result = $validator->verify($value);
```

The `$result` contains the validation status and validated value. 

```php
if ($result->isValid()) {
    echo $result->value(); // validated value
} else {
    var_dump($result->getErrorMessage()); // error messages
}
```

### Validating a Form Input

```php
$form = $vb->form('user')
    ->add('name', $vb->text([Required::class]))
    ->add('age', $vb->number())
;
$result = $form->verify($_POST);
```

The `$result` contains child result for each element in the form. 

```php
if ($result->isValid()) {
    echo $result->value(); // validated values
} else {
    // access elements in the form.
    foreach($result as $key => $element) {
        if (!$element->isValid()) {
            echo $element->getErrorMessage(); // error messages
        }
    }
}
```

### Nested Form

Simply add another form object in a form object. 


```php
$form = $vb->form('user')
    ->add('name', $vb->text([Required::class]))
    ->add('age', $vb->number())
    ->add('address', 
        $vb->form()
            ->add('address', $vb->text())
            ->add('countryCode', $vb->text([StringLength=>['length'=>3]))

    )
;
$result = $form->verify($_POST);
```


### One-To-Many Forms

use `addRepeatedForm` method to add a one-to-many form in another form. 

```php
$vb = new ValidatorBuilder();
$form = $vb->form('user')
    ->add('name', $vb->text([Required::class]))
    ->add('age', $vb->number())
    ->addRepeatedForm('address', 
        $vb->form()
            ->add('address', $vb->text())
            ->add('country', $vb->text())
    )
;
$result = $form->verify($_POST);
```


### Validating Array



Locale
------

specify locale (i.e. 'en', 'ja', etc.) when building the `ValidationBuilder`: 

```php
$vb = new ValidatorBuilder('ja');
```

Or specify the folder in which the builder can find the message and type definition files: 

```php
$vb = new ValidatorBuilder('/dir/to/my/locale');
```

The folder must contain: `validation.message.php` and `validation.types.php` files.

### `validation.message.php`


### `validation.types.php`


Predefined Types
================

t.b.w.

Text Types
----------

### Text

### email
### number
### tel

DateTime Types
--------------

### date
### datetime
### month
### time
### timeHi

multiple input
--------------

### dateYMD


Predefined Filters
==================

t.b.w.

about priorities
- Filters that may change the value.
    - FILTER
    - VALIDATOR
    - CONVERTER
    - DEFAULT
- Filters that only validates the format.
    - REQUIRED
    - CHECKS
    - USER_CHECK

Filters
-------

`Filter*` filters may change the value but no validity check. 

### FilterArrayToValue

- convert array input to a single text value. 
- arguments: `['fields' => ['y', 'm'], 'format' => '%d.%d', 'implode' => '...']`
    - `fields`: required. specify list of array key names. 
    - `format`: optional. spritnf format for the array values. 
    - `implode`: optional. if `format` is not defined, implode values with the character (default is '-').

sample code: 

```php
$filter = new FilterArrayToValue([
    'fields' => ['y', 'm', 'd'],
    'format' => '%d.%02d.%02d',
]);
```

### FilterMbKana


Validators
---------

`Validate` filters may change the value as well as checks for the validity of the input. 

### ValidateValidUtf8

- checks if the input value is a valid UTF-8 characters. 
- priority: FilterInterface::PRIORITY_SECURITY_FILTERS
- errors: on error, replaces the input value with an empty string (''). 
  - FilterValidUtf8::INVALID_CHAR : invalid UTF-8 characters. 
  - FilterValidUtf8::ARRAY_INPUT  : input is an array. 

### ValidateDateTime

- converts string input into `\DateTimeImmutable` object. 
- arguments: `['format' => 'YY.m.d']`.
  - format: optional. if set uses `\DateTimeImmutable::createFromFormat`
  
```php
$filter = new ConvertDateTime(['format' => 'm/d/Y']);
$result = $filter(new Result('04/01/2019'));
$date = $result->value(); // should be DateTimeImmutable object. 
```

### ValidateInteger

### ValidateFloat


Converters
----------

`Converter` filter may change the value. 

### ConvertStringCases
### ConvertTrim

Default Checks
--------------

### DefaultValue

- set value to a default `$default` if the input is null or empty. 
- arguments: `['default' => 'some value']`
  - default: optional. if not set uses empty string ('').

### DefaultNull

- set value to `null` if the input is null or empty. 
- arguments: none. 

### DefaultEmpty

- set value to `""` (empty string) if the input is null or empty. 
- arguments: none. 


Require Checks
--------------

### Required

- validates the input value is not null nor empty string. 
- aborts further validations if failed. 
- arguments: none. 

### RequiredIf

- validates the input value is not null nor empty string, 
  if the name `field` is set, or if `field` value is `value`.
- aborts further validations if failed. 
- arguments: `['field' => '', 'value' => '']`
  - field: required. set other field name to refer to. 
  - value: optional. set value (or values in array) if the other field has the specified value. 

```php
$required = new RequiredIf(['field' => 'type', 'value' => 'check-me']);
```

Other Checks
------------

### StringLength

- checks the input value's character length. 
  set any of the arguments, `length`, `max`, or `min`, 
  but probably does not make sense to set all options. 
- arguments: `['length' => 5, 'max' => 6, 'min' => 7]`
  - length: optional. specify the exact length of the input string. 
  - max: optional. set the maximum length of the input string. 
  - min: optional. set the minimum length of the input string. 


### ConfirmWith

- confirm the input value by comparing with another input value. 
- not tested, yet.


### InArray

- checks the input value is defined in the given arrays. 
- not tested, yet.


### code
### RegEx
### matches
### MbCheckKana
### Email


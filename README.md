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
    StringLength::class => ['max' => 12],
]);
```

To create a validation, do: `$vb->{type}($option_array)`. 

The `$result` contains the validation status and validated value. 

```php
$result = $validator->verify($value);
if ($result->isValid()) {
    echo $result->value(); // validated value
} else {
    var_dump($result->getErrorMessage()); // error messages
}
```

### Validating a Form Input

```php
$form = $vb->form('user')
    ->add('name', $vb->text([
        Required::class
    ]))
    ->add('email', $vb->email([
        Required::class,
        StringCases::class => [StringCases::TO_LOWER],
        ConfirmWith::class => [ConfirmWith::FIELD => 'email_check'],
    ]));
$result = $form->verify([ // or simply verify $_POST here... 
    'name' => 'MY NAME',
    'email' => 'Email@Example.Com',
    'email_check' => 'Email@Example.Com',
]);
```
Another way to create a validation, do: `$vb($option_array)`, where the type maybe specified in `$option_array`. 

The `$result` contains child result for each element in the form. 

```php
if ($result->isValid()) {
    echo $result->getChild('name')->value();  // 'my name'
    echo $result->getChild('email')->value(); // 'email@example.com'
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
$address = $vb->form()
    ->add('zip', $vb([
        'type' => 'digits',
        Required::class,
        StringLength::class => [StringLength::LENGTH => 5],
    ]))
    ->add('address', $vb([
        'type' => 'text',
        Required::class,
    ]))
    ->add('region', $vb([
        'type' => 'text',
        Required::class,
        InArray::class => [
            InArray::REPLACE => [
                'abc' => 'ABC Country',
                'def' => 'DEF Region',
            ],
        ],
    ]));

$form = $vb->form()
    ->add('name', $vb->text([Required::class]))
    ->add('address', $address);
```

A valid input may look like;

```php
$input = [
    'name' => 'test-nested',
    'address' => [
        'zip' => '12345',
        'address' => 'city, street 101',
        'region' => 'abc',
    ]
];
$result = $form->verify($input);
echo $result->getChild('address')->getChild('region')->value(); // 'ABC Country'
```

### One-To-Many Forms

use `addRepeatedForm` method to add a one-to-many form in another form. 

```php
$posts = $vb->form()
    ->add('title', $vb->text([
        Required::class,
    ]))
    ->add('publishedAt', $vb->date())
    ->add('size', $vb->integer([
        Required::class,
    ]));
$form = $vb->form()
    ->add('name', $vb->text([Required::class]))
    ->addRepeatedForm('posts', $posts);
```

A valid input may look like:

```php
$input = [
    'name' => 'test-one-to-many',
    'posts' => [
        ['title' => 'first title', 'size' => 1234],
        ['title' => 'more tests here', 'publishedAt' => '2019-04-01', 'size' => 2345],
    ],
];
$result = $form->verify($input);
```


### Validating Array

To validate an array input, specify `multiple` when constructing a chain, as;

```php
$tests = $vb->text([
    'multiple' => true, // specify multiple!
]);
$result = $tests->verify(['test', 'me']);
```



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

|filter|option|
|----|----|
|ValidateUtf8String|--|
|DefaultEmpty|--|

### email

|filter|option|
|----|----|
|ValidateUtf8String|--|
|DefaultEmpty|--|
|Match|type: Match::EMAIL|

### integer

|filter|option|
|----|----|
|ValidateInteger|--|
|DefaultNull|--|

### float

|filter|option|
|----|----|
|ValidateFloat|--|
|DefaultNull|--|

### tel

DateTime Types
--------------

### date

|filter|option|
|----|----|
|ValidateDateTime|--|
|DefaultNull|--|

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

### FilterMbString

- convert Japanese kana style. 
- arguments: `['type' => 'convert']`
  - type: required. conversion option for `mb_convert_kana`. default is MB_ZEN_KANA. 
- available type values:
  - `FilterMbString::MB_ZEN_KANA`: convert han-kaku-kana to zen-kaku-kana.
  - `FilterMbString::MB_HANKAKU`: convert zen-kaku-alphabets and digits to han-kaku. 
  - `FilterMbString::MB_MB_ZENKAKU`: convert all characters to zen-kaku.
  - `FilterMbString::MB_HAN_KANA`: convert all characters to han-kaku, if possible.
  - `FilterMbString::MB_HIRAGANA`: convert all kana to zen-kaku-hiragana.
  - `FilterMbString::MB_KATAKANA`: convert all kana to zen-kaku-katakana.
- [ ] not tested, yet.

```php
$filter = new FilterMbString(['type' => FilterMbString::MB_HANKAKU]);
$result = $filter(new Result('ｚｅｎｋａｋｕ＠ｅｘａｍｐｌｅ．ｃｏｍ'));
echo $result->value(); // zenkaku@example.com
```

Validators
---------

`Validate` filters may change the value as well as checks for the validity of the input. 

### ValidateValidUtf8

- checks if the input value is a valid UTF-8 characters. 
- priority: FilterInterface::PRIORITY_SECURITY_FILTERS
- errors: on error, replaces the input value with an empty string (''). 
  - `FilterValidUtf8::INVALID_CHAR` : invalid UTF-8 characters. 
  - `FilterValidUtf8::ARRAY_INPUT`  : input is an array. 

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

- checks the input value is numeric, and converts the value to an integer. 
- errors: if the value is not numeric, or the value is an array. 
- arguments: none.

### ValidateFloat

- checks the input value is numeric, and converts the value to a float. 
- errors: if the value is not numeric, or the value is an array. 
- arguments: none.


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
- arguments: `['length' => 5, 'max' => 6, 'min' => 7, 'message' => 'error message']`
  - length: optional. specify the exact length of the input string. 
  - max: optional. set the maximum length of the input string. 
  - min: optional. set the minimum length of the input string. 
  - message: optional. set error message. 


### ConfirmWith

- confirm the input value by comparing with another input value. 
- arguments: `['with' => 'field_name_to_confirm_with']`
  - with: optional. if not set, uses `{name}_confirmation`
- [ ] not tested, yet.


### InArray

- checks the input value is defined in the given arrays. 
- arguments: `['choices' => ['a' => 'A', 'b' => 'B',...], 'replace' => false, 'strict' => true]`
  - choices: required. specify the available choices as hash array.
  - replace: optional. replace the value with the choice. default false. 
  - strict: optional. strict option when checking the value. default true. 
- [ ] not tested, yet.

```php
$filter = new InArray([
    'choices' => [
        $obj1->getKey(), $obj1,
        $obj2->getKey(), $obj2,
    ],
    'replace' => true,
    'strict' => true,
]);
```

### RegEx

- checks for regular expression. 
- arguments: `['pattern' => '[A-D][0-9]{1}', 'message' => 'error message']`
  - pattern: required. set regular expression pattern. 
    the pattern is valuated as `/\A{$pattern}\z/us`. 
  - message: optional. set error message. 
- [ ] not tested, yet.

### Match

- validates input string with predefined types using `filter_var`. 
- as a default, error message is selected based on the type. 
- arguments: `['type' => 'filter_var_filter', 'message' => 'error message']`
  - type: required. 
  - message: optional. set error message when failed to validate. 
- types are:
  - `Match::IP`: validates IP address. 
  - `Match::EMAIL`: validates email address. 
  - `Match::URL`: validates URL.
  - `Match::MAC`: validates MAC address.
- [ ] not tested, yet.

### code
### MbCheckKana
### Email


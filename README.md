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

Create a `ValidationBuilder` instance, then create a validator;

```php
$vb = new ValidatorBuilder();
$validator = $vb->text([
    StringLength::class => ['max' => 12],
]);
```

- To create a validation, do: `$vb->{type}($option_array)`. 
  - `type`: type of validation, as defined in the subsequent section. 
  - `option_array`: array of options. `StringLength` is a filter (also called validator). 
    - `multiple`: boolean. optional. to validate against a multiple (i.e. array) value. 
    - `type`: string. optional. can specify type in the option as well. 
    - `filters`: array. deprecated way to specify filters. 
    - all other options are considered as filters. 

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
$form = $vb->form()
    ->add('name', $vb->text([
        Required::class,
        StringCases::class => [StringCases::TO_LOWER, StringCases::UC_WORDS],
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
    echo $result->getChild('name')->value();  // 'My Name'
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


### Validating an Array

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

A php file that returns an array of error messages, 

```php
return [
    'filter_error_type' => 'error message here',
    ...
];
```

### `validation.types.php`

A php file that returns an array of filters for each type, 

```php
return [
    'type-name' => [
        'filter-name', 
        'filter-with-option' => ['options' => 'here'],
    ], ...
];
```

Predefined Types
----------

t.b.w.

### Text

- Validates a valid UTF-8 string, maximum 1MB of `string`. 
- predefined filters: 
  - ValidateUtf8String
  - DefaultValue: empty string

### email

- Validates a valid UTF-8 string, 
  maximum 1MB of input, and a valid email `string`. 
- predefined filters: 
  - ValidateUtf8String
  - DefaultValue: empty string
  - Match: EMAIL

### integer

- Validates a valid UTF-8 numeric string, 
  and casted to an `integer`. 
- predefined filters: 
  - ValidateInteger
  - DefaultValue: NULL

### float

- Validates a valid UTF-8 numeric string, 
  and casted to a `float`. 
- predefined filters: 
  - ValidateFloat
  - DefaultValue: NULL

### date

- Validates a valid UTF-8 date format string, 
  and convert to a `\DateTimeImmutable` object. 
- predefined filters: 
  - ValidateDateTime
  - DefaultValue: NULL

### digits

- Validates a valid UTF-8 string with digits only, 
  and casted to a `string`. 
- predefined filters: 
  - ValidateDateTime
  - DefaultValue: empty string

### datetime
### month
### time
### timeHi
### dateYMD


Predefined Filters
-------

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

### ValidateMbString

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

### DefaultValue

- set value to a default `$default` if the input is null or empty. 
- arguments: `['default' => 'some value']`
  - default: optional. if not set uses empty string ('').

### Required

- validates the input value is not null nor empty string. 
- aborts further validations if failed. 
- arguments: `[Required::NULLABLE => true]`. 
  - `Required::NULLABLE`: optional, default false. breaks validation chain 
    if the value is empty (i.e. either null, empty string, or empty array).

### RequiredIf

- validates the input value is not null nor empty string, 
  if the name `field` is set, or if `field` value is `value`.
- aborts further validations if failed. 
- arguments: `[RequiredIf::FIELD => 'other', RequiredIf::VALUE => 'val']`
  - `RequiredIf::FIELD`: string. required. set other field name to refer to. 
  - `RequiredIf::VALUE`: string, integer, float, or array. optional. 
     set value (or values in array) if the other field has the specified value. 
  - `Required::NULLABLE`: optional, default false. breaks validation chain 
    if the value is empty (i.e. either null, empty string, or empty array).

```php
$required = new RequiredIf([
    RequiredIf::FIELD => 'type', 
    RequiredIf::VALUE => 'check-me',
    RequiredIf::NULLABLE => true,
]);
```

### Nullable

- breaks validation chain/loop if the input value is empty. 
  stops further validations which may fail if the input is an empty value. 
  thus, this filter allows NULLABLE value, regardless of the subsequent filter. 
- use it for non-required field but have certain filters (RegEx) which may fail if the input is empty. 
- arguments: none.
- [ ] not tested, yet. 

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
- arguments: `['choices' => ['A', 'B',...]` or `['replace' => ['a' => 'A', 'b' => 'B']]`. 
  must specify either `choices` or `replace` option. 
  - choices: optional array. specify the available choices as an array.
  - replace: optional hashed array. replace the value. 
  - strict: optional. strict option when checking the value. default true. 

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
- arguments: `[RegEx::PATTERN => '[A-D][0-9]{1}', RegEx::MESSAGE => 'error message']`
  - `RegEx::PATTERN`: required. set regular expression pattern. 
    the pattern is valuated as `/\A{$pattern}\z/us`. 
  - `RegEx::MESSAGE`: optional. set error message. 
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

### StringCases
### StringTrim
### ValidateDigits


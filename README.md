Validation
==========

A validation component for,

- nested and/or one-to-many forms,
- good multi-byte support (i.e. Japanese), 


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
$vb = new ValidatorBuilder();
$form = $vb->form('user')
    ->add('name', $vb->text([
        'filters' => [Required::class]
    ]))
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
$vb = new ValidatorBuilder();
$form = $vb->form('user')
    ->add('name', $vb->text([
        'filters' => [Required::class]
    ]))
    ->add('age', $vb->number())
    ->add('address', 
        $vb->form()
            ->add('address', $vb->text())
            ->add('country', $vb->text())
    )
;
$result = $form->verify($_POST);
```


### One-To-Many Forms

use `addRepeatedForm` method to add a one-to-many form in another form. 

```php
$vb = new ValidatorBuilder();
$form = $vb->form('user')
    ->add('name', $vb->text([
        'filters' => [Required::class]
    ]))
    ->add('age', $vb->number())
    ->addRepeatedForm('address', 
        $vb->form()
            ->add('address', $vb->text())
            ->add('country', $vb->text())
    )
;
$result = $form->verify($_POST);
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
----------------

t.b.w.

### Text

*   text
*   email
*   number
*   tel

### DateTime

*   date
*   datetime
*   month
*   time
*   timeHi

### multiple input

*   dateYMD


Predefined Filters
------------------

t.b.w.

### Filters

*   FilterArrayToValue
    * convert array input to a single text value. 
    * arguments are ['fields' => ['y', 'm'], 'format' => '%d.%d']
        * `fields`: specify list of array key names. required.
        * `format`: spritnf format for the array values. 
        * `implode`: if `format` is not defined, implode values with the character (default is '-').

*   FilterValidUtf8
*   MbConvertKana
*   StringCases
*   FilterDateTime


*   DefaultValue
*   DefaultNull
*   DefaultEmpty


### Validations

*   Required
*   RequiredIf


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


# Translation
A file based translation component without dependencies.

[![Build Status](https://travis-ci.org/michaelspiss/translation.svg?branch=master)](https://travis-ci.org/michaelspiss/translation)
[![Coverage Status](https://coveralls.io/repos/github/michaelspiss/translation/badge.svg?branch=master)](https://coveralls.io/github/michaelspiss/translation?branch=master)
[![Latest Stable Version](https://poser.pugx.org/michaelspiss/translation/v/stable)](https://packagist.org/packages/michaelspiss/translation)
[![License](https://poser.pugx.org/michaelspiss/translation/license)](https://packagist.org/packages/michaelspiss/translation)

It supports **every file format** you throw at it, **pluralization**,
**placeholders**, is **fully tested** and **documented**.

## Installation

To install the package with Composer, simply run the following command:
```
$ composer require michaelspiss/translation
```

## Basic usage
Instantiating a new Translator is easy: Simply pass it the default locale
('en' in this case) and the path to your translations directory!

```php
require 'vendor/autoload.php'; // composer autoloader

use MichaelSpiss\Translation\Translator;

$translator = new Translator('en', 'path/to/translations');
```

The component uses keys in the dot-notation to identify which string to load.
To get a string in a specific language use the trans() method and pass it the key:
```php
$translator->trans('message.hello_world');
```
In this case the string would be found in `path/to/translations/en/message.php`.
The file itself would look like "message.php" below.

## Folder structure
Language strings are stored in files in the directory you passed the Translator
during initialization. Within this directory each subfolder represents a supported
language and holds the translation files.
```
translations/
 |- en/
 |   |- message.php
 |- de/
 |- fr/
```
A translation file would simply look like the following:

###### message.php
```php
<?php

return [
    'hello_world' => 'Hello World!'
];
```
Even though only .php files are supported out-of-the-box, translation files 
can be of every format. For more information, please see the [corresponding wiki article](https://github.com/michaelspiss/translation/wiki/Custom-Loader).


## Setting the locale
Normally, the locale is only set once, at the initialization.
But the component also allows you to change the locale mid-way, of course:
```php
$translator->setLocale('de');
```
Additionally you can request single translation strings in a specific language
without setting a new locale and resetting it afterwards:
```php
$translator->trans('message.hello_world', [], 'de');
// or:
$translator->transChoice('message.choice', 1, [], 'de');
```

## Using placeholders
In your translation files, a placeholder is defined by putting curly braces
around the placeholder name (upper- and lowercase letters only):

###### message.php
```php
<?php

return [
    'with_placeholder' => 'Hello {placeholder}!'
];
```
Now you can add an array to your trans() call containing values for the
placeholders:
```php
$translator->trans('message.with_placeholder', ['placeholder' => 'World']);
// returns "Hello World!"
```
You can have as many placeholders in a string as you like!

## Pluralization
To enable pluralization for a string simply add a pipe-character between
singular and plural form in your translation file:

###### message.php
```php
<?php

return [
    'simple_pluralization' => 'Dog | Dogs'
];
```
The second option is a bit more advanced and actually allows you to set
your own rules:
```php
<?php

return [
    'advanced_pluralization' => '{0} None | [1,10] Some | [11,*] Many'
];
```
You can use the following expressions:

| Expression | Meaning|
| :---: | --------------- |
| {3} | match exactly 3 |
| [1,10] | match everything from 1 to 10, **including** 1 and 10 |
| [11,*] | match everything from 11 to infinite **including** 11 |
| ]10,20] | match everything from 10 to 20, **excluding** 10, **including** 20 |
| ]10,20[ | match everything from 10 to 20, **excluding** 10 and 20 |
| [10,20[ | match everything from 10 to 20, **including** 10, **excluding** 20 |

Expressions also support integers and floats!

To get the string use the transChoice() method, which takes an additional
`number` argument (type int or float):
```php
$translator->transChoice('message.simple_pluralization', 4);
// returns "Dogs"

$translator->transChoice('message.advanced_pluralization', 8);
// returns "Some"
```
## Read more
If you want further information on the [`Translator`'s methods](https://github.com/michaelspiss/translation/wiki/Method-overview)
or [how to use other file formats than .php](https://github.com/michaelspiss/translation/wiki/Custom-Loader)
check out [this project's wiki](https://github.com/michaelspiss/translation/wiki)!

### PHP Requirements
* PHP >= 7.0.0

### License
MIT
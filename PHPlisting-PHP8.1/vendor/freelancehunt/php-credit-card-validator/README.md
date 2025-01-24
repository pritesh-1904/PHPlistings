# PHP Credit Card Validator

[![Build Status](https://travis-ci.com/freelancehunt/php-credit-card-validator.svg?branch=master)](https://travis-ci.com/freelancehunt/php-credit-card-validator) 
[![codecov](https://codecov.io/gh/freelancehunt/php-credit-card-validator/branch/master/graph/badge.svg)](https://codecov.io/gh/freelancehunt/php-credit-card-validator) 
![PHP from Packagist](https://img.shields.io/packagist/php-v/freelancehunt/php-credit-card-validator.svg)
[![Packagist](https://img.shields.io/packagist/v/freelancehunt/php-credit-card-validator.svg)](https://packagist.org/packages/freelancehunt/php-credit-card-validator)
[![Packagist](https://img.shields.io/packagist/dt/freelancehunt/php-credit-card-validator.svg)](https://packagist.org/packages/freelancehunt/php-credit-card-validator)
[![License](https://img.shields.io/github/license/freelancehunt/php-credit-card-validator.svg)](https://coveralls.io/github/freelancehunt/php-credit-card-validator?branch=master) 

Validates popular debit and credit cards numbers against regular expressions and Luhn algorithm.
Also validates the CVC and the expiration date.

Since original project seems to be abandoned, we plan to maintain this fork. 

# Requirements
PHP 7.1+. We don't plan to support [EOL](http://php.net/supported-versions.php) PHP versions.  

Require the package in `composer.json`

```json
"require": {
    "freelancehunt/php-credit-card-validator": "3.*"
},
```
## Usage

### Validate a card number knowing the type:

```php
$card = CreditCard::validCreditCard('5500005555555559', CreditCard::TYPE_MASTERCARD);
print_r($card);
```

Output:

```
Array
(
    [valid] => 1
    [number] => 5500005555555559
    [type] => mastercard
)
```

### Validate a card number against several types:

```php
$card = CreditCard::validCreditCard('5500005555555559', [CreditCard::TYPE_VISA, CreditCard::TYPE_MASTERCARD]);
print_r($card);
```

Output:

```
Array
(
    [valid] => 1
    [number] => 5500005555555559
    [type] => mastercard
)
```

### Validate a card number and return the type:

```php
$card = CreditCard::validCreditCard('371449635398431');
print_r($card);
```

Output:

```
Array
(
    [valid] => 1
    [number] => 371449635398431
    [type] => amex
)
```

### Validate the CVC

```php
$validCvc = CreditCard::validCvc('234', CreditCard::TYPE_VISA);
var_dump($validCvc);
```

Output:

```
bool(true)
```

### Validate the expiration date

```php
$validDate = CreditCard::validDate('2013', '07'); // past date
var_dump($validDate);
```

Output:

```
bool(false)
```

## Tests

Execute the following command to run the unit tests:

    vendor/bin/phpunit
